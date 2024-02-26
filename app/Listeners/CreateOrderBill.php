<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException as ValidationsException;
use App\Events\BillCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateOrderBill
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderCreated  $event
     * @return void
     */
    public function handle(OrderCreated $event)
    {
        $bill = DB::transaction(function () use ($event) {
            $user = User::find($event->order->user_id);

            $bill = Bill::create([
                'user_id' => $user->store_main_user_id ?? $user->id,
                'created_by' => $user->id,
                'status' => 'pending',
                'business_name' => $event->order->business_name,
                'customer_id' => $event->order->customer_id,
                'customer_name' => $event->order->customer_name,
                'customer_email' => $event->order->customer_email,
                'customer_mobile' => $event->order->customer_mobile,
                'customer_notes' => $event->order->customer_notes,

                'reference_id' => $event->order->number,

                'expiry_date' => 30,
                'expiry_hours' => $request->expiry_hours ?? 0,
                'expiry_minutes' => $request->expiry_minutes ?? 0,
                'due_date' => date('Y-m-d', strtotime(str_replace('/', '-', $event->order->created_at))),

                'add_discount' => $event->order->add_discount  ? "on" : 0,
                'discount_type' => $event->order->add_discount  ? $event->order->discount_type : false,
                'discount_value' => $event->order->add_discount  ? $event->order->discount_value : null,

                'add_tax' => $event->order->add_tax ? "on" : false,
                'tax_name' => $event->order->add_tax ? $event->order->tax_name : null,
                'tax_value' => $event->order->add_tax ? $event->order->tax_value : null,

                'send_sms' => false,
                'send_email' => false,

                'source' => 'pos',
            ]);

            foreach ($event->order->items as $item) {
                BillItem::create([
                    'bill_id' => $bill->id,
                    'product_name' => $item->product_name,
                    'product_price' => $item->product_price,
                    'quantity' => $item->quantity,
                    'total' => $item->quantity * $item->product_price,
                ]);
            }

            $sub_total = $bill->items->sum('total');
            $discount = 0;
            $vat = 0;
            $payment_fees = 0;

            if ($user->pay_fees == "client") {
                $payment_fees = ($sub_total * ($user->credit_cards_percentage / 100)) + $user->credit_cards_fixed;
            }

            if ($event->order->add_discount) {
                switch ($event->order->discount_type) {
                    case 'fixed':
                        $discount = $event->order->discount_value;
                        break;
                    case 'percentage':
                        $discount = ($sub_total + $payment_fees) * $event->order->discount_value / 100;
                        break;
                }
            }

            if ($event->order->add_tax) {
                $vat = ($sub_total + $payment_fees - $discount) * $event->order->tax_value / 100;
            }

            $bill->payment_fees = $payment_fees;
            $bill->discount = $discount;
            $bill->vat = $vat;
            $bill->number = $bill->getNumber();
            $bill->sub_total = $sub_total;
            $bill->total = $sub_total + $payment_fees - $discount + $vat;
            $bill->fixed_total = $sub_total + $payment_fees - $discount + $vat;
            if ($bill->total <= 0) {
                throw ValidationsException::withMessages(['total' => __('The total must be greater than 0')]);
            }
            $bill->save();
            return $bill;
        });

        event(new BillCreated($bill));
    }
}
