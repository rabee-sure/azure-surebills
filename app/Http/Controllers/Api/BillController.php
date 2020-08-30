<?php

namespace App\Http\Controllers\Api;

use App\Application;
use App\Bill;
use App\BillItem;
use App\Customer;
use App\Events\BillCreated;
use App\Events\BillStatusUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\BillApiRequest;
use App\Http\Requests\CheckBillApiRequest;
use App\Http\Requests\PayBillRequest;
use App\Http\Resources\BillApiResource;
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
        $application = Application::whereId($request->application_id)->whereSecret($request->application_secret)->first();
        $user = $application->user;

        $customer = Customer::updateOrCreate([
            'user_id' => $user->id,
            'mobile' => $request->customer_mobile,
        ],[
            'name' => $request->customer_name, 
            'email' => $request->customer_email,
        ]);

        $bill = Bill::create([
            'user_id' => $user->id,
            'application_id' => $application->id,

            'business_name' => $user->business_name,
            'customer_id' => $customer->id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_mobile' => $request->customer_mobile,
            'customer_notes' => $request->customer_notes,

            'expiry_date' => $request->expiry_date,
            'expiry_hours' => $request->expiry_hours,
            'expiry_minutes' => $request->expiry_minutes,
            'due_date' => Carbon::parse($request->due_date),

            'add_discount' => $request->add_discount,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,

            'add_tax' => $request->add_tax,
            'tax_name' => $request->tax_name,
            'tax_value' => $request->tax_value,

            'send_sms' => $request->send_sms,
            'send_email' => $request->send_email,
            'reference_id' => $request->reference_id,
        ]);

        if($user->settings->add_tax){
            $bill->add_tax = $user->settings->add_tax;
            $bill->tax_value = $user->settings->tax_value;
        }
        if($user->settings->create_send_sms){
            $bill->send_sms = $user->settings->create_send_sms;
        }
        if($user->settings->create_send_email){
            $bill->send_email = $user->settings->create_send_email;
        }
        $bill->save();


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
        $bill->status = 'pending';
        $bill->save();
        
        event(new BillCreated($bill));

        return new BillApiResource($bill);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show( $id, CheckBillApiRequest $request)
    {
        $application = Application::whereId($request->application_id)->whereSecret($request->application_secret)->first();
        $bill = Bill::find($id);

        if(isset($application) && $application->id == $bill->application_id){
            return new BillResource($bill);
        }else{
            return response()->json(['success' => false]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cancel($id, CheckBillApiRequest $request)
    {
        $application = Application::whereId($request->application_id)->whereSecret($request->application_secret)->first();
        $bill = Bill::find($id);

        if(isset($application) && $application->id == $bill->application_id){
            if($bill->status != 'canceled'){
                $bill->status = 'canceled';
                $bill->save();
                event( new BillStatusUpdated($bill) );
            }

            return new BillResource($bill);
        }else{
            return response()->json(['success' => false]);
        }
    }    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function timeout($id, CheckBillApiRequest $request)
    {
        $application = Application::whereId($request->application_id)->whereSecret($request->application_secret)->first();
        $bill = Bill::find($id);

        if(isset($application) && $application->id == $bill->application_id){
            if($bill->status != 'expired'){
                $bill->status = 'expired';
                $bill->save();
                event( new BillStatusUpdated($bill) );
            }

            return new BillResource($bill);
        }else{
            return response()->json(['success' => false]);
        }
    }
}
