<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Bill;
use App\Models\OfflineTransaction;

class AddOldCashBillsOfflineTransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bills = Bill::whereIn('status', ['paid_cash', 'paid_bank_transfer', 'refunded_cash', 'refunded_bank_transfer'])->get();

        foreach($bills as $bill){
            // add paid credit transaction
            $transaction = new OfflineTransaction;
            $transaction->user_id     = $bill->user_id;
            $transaction->bill_id     = $bill->id;
            $transaction->type        = 'credit';
            $transaction->amount      = $bill->sub_total + $bill->vat - $bill->discount;
            $transaction->reference   = $bill->number;
            $transaction->description = 'Bill ' . $bill->number . ' - ' . $bill->customer_name;
            $transaction->transaction_source = 'bill';
            $transaction->saveIfUnique();

            if($bill->status == 'paid_cash' || $bill->status == 'paid_bank_transfer'){
                // if has refund amount add partial refunded offline trnscation
                if($bill->refund_amount > 0){
                    $order_max = OfflineTransaction::where('bill_id', $bill->id)->max('order');
                    $transaction = new OfflineTransaction;
                    $transaction->user_id     = $bill->user_id;
                    $transaction->bill_id     = $bill->id;
                    $transaction->type        = 'debit';
                    $transaction->amount      = $bill->refund_amount;
                    $transaction->reference   = $bill->number;
                    $transaction->description = 'PARTIAL REFUND Bill ' . $bill->number . ' - ' . $bill->customer_name;
                    $transaction->transaction_source = 'refund';
                    $transaction->order = $order_max+1;
                    $transaction->save();
                }
            }elseif($bill->status == 'refunded_cash' || $bill->status == 'refunded_bank_transfer'){
                // add refunded offline trnscation
                $order_max = OfflineTransaction::where('bill_id', $bill->id)->max('order');

                $transaction = new OfflineTransaction;
                $transaction->user_id     = $bill->user_id;
                $transaction->bill_id     = $bill->id;
                $transaction->type        = 'debit';
                $transaction->amount      = $bill->sub_total + $bill->vat - $bill->discount;
                $transaction->reference   = $bill->number;
                $transaction->description = 'REFUND Bill ' . $bill->number . ' - ' . $bill->customer_name;
                $transaction->transaction_source = 'refund';
                $transaction->order = $order_max+1;
                $transaction->save();
            }
        }
    }
}
