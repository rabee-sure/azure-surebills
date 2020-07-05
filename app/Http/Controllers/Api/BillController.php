<?php

namespace App\Http\Controllers\Api;

use App\Bill;
use App\BillItem;
use App\Customer;
use App\Events\BillCreated;
use App\Events\BillPaid;
use App\Http\Controllers\Controller;
use App\Http\Requests\BillApiRequest;
use App\Http\Requests\PayBillRequest;
use App\Http\Resources\BillResource;
use App\OauthClient;
use App\PaymentLog;
use App\Payment\Facades\Payment;
use App\Payment\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BillController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BillApiRequest $request)
    {
        $client = OauthClient::findByRequest($request);

        $customer = Customer::updateOrCreate([
            'user_id' => auth()->user()->id,
            'mobile' => $request->customer_mobile,
        ],[
            'name' => $request->customer_name, 
            'email' => $request->customer_email,
        ]);

        $bill = Bill::create([
            'user_id' => auth()->user()->id,
            'business_name' => auth()->user()->business_name,
            'customer_id' => $customer->id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_mobile' => $request->customer_mobile,
            'customer_notes' => $request->customer_notes,

            'expiry_date' => $request->expiry_date,
            'due_date' => Carbon::parse($request->due_date),

            'add_discount' => $request->add_discount,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,

            'add_tax' => $request->add_tax,
            'tax_name' => $request->tax_name,
            'tax_value' => $request->tax_value,

            'send_sms' => $request->send_sms,
            'send_email' => $request->send_email,
            'client_id' => $client->id,
        ]);

        foreach ($request->items as $item) {
            BillItem::create([
                'bill_id' => $bill->id,
                'product_name' => $item['name'],
                'product_price' => $item['price'],
                'quantity' => $item['quantity'],
                'total' => $item['quantity']*$item['price'],
            ]);
        }

        $sub_total = $bill->items->sum('total');
        $discount = 0;
        $vat = 0;
        if($request->add_discount){
            switch ($request->discount_type) {
                case 'fixed':
                    $discount = $request->discount_value;
                    break;
                case 'percentage':
                    $discount = $sub_total * $request->discount_value / 100;
                    break;
            }
        } 

        if($request->add_tax){
           $vat = ($sub_total -$discount) * $request->tax_value /100;
        }

        $bill->discount = $discount;
        $bill->vat = $vat;
        $bill->number = $bill->getNumber();
        $bill->sub_total = $sub_total;
        $bill->total = $sub_total - $discount + $vat;
        $bill->save();
        
        event(new BillCreated($bill));

        return new BillResource($bill);
    }

}
