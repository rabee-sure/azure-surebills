<?php

namespace App\Http\Controllers\Api;

use App\Events\BillCreated;
use App\Events\BillStatusUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\BillApiRequest;
use App\Http\Requests\CheckBillApiRequest;
use App\Http\Resources\BillApiResource;
use App\Http\Resources\BillResource;
use App\Models\Application;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Customer;
use App\Rules\AmountPartialRefund;
use App\Rules\AmountPartialRefundGTBalance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Tag;

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
        $application = $request->application;
        $user = $application->user ?? null;

        if($request->application_name){
            $application = $this->getApplication($application, $request);
        }

        Bill::where('reference_id', $request->reference_id)->where('user_id', $application->user_id ?? null)->where('status', 'pending')->update(['status' => 'canceled']);

        $customer = Customer::updateOrCreate([
            'user_id' => $user->id,
            'mobile' => $request->customer_mobile,
        ],[
            'name' => $request->customer_name, 
            'email' => $request->customer_email,
            
            'bullding_no' => $request->customer_bullding_no,
            'street_name' => $request->customer_street_name,
            'district' => $request->customer_district,
            'city' => $request->customer_city,
            'postal_code' => $request->customer_postal_code,
            'additional_no' => $request->customer_additional_no,
            'other_buyer_id' => $request->customer_other_buyer_id,
        ]);

        $send_sms = $request->send_sms === null ? $user->settings->create_send_sms : $request->send_sms;
        $send_email = $request->send_email === null ? $user->settings->create_send_email : $send_email = $request->send_email;

        $bill = Bill::create([
            'user_id' => $user->id,
            'status' => 'pending',
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

            'add_discount' => $request->add_discount ?? false,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,

            'add_tax' => $request->add_tax ?? false,
            'tax_name' => $request->tax_name,
            'tax_value' => $request->tax_value,

            'send_sms' => $send_sms,
            'send_email' => $send_email,
            'reference_id' => $request->reference_id,
            'is_redirect' => $request->is_redirect,

            'bill_redirect_url' => $request->redirect_url,
            'bill_webhook_url' => $request->webhook_url,
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

        if($request->add_tax !== null){
            $bill->add_tax = $request->add_tax;
            $bill->tax_value = $request->tax_value;
            $vat = ($sub_total -$discount) * $request->tax_value /100;

        }elseif($user->settings->add_tax){
            $bill->add_tax = $user->settings->add_tax;
            $bill->tax_value = $user->settings->tax_value;      
            $vat = ($sub_total -$discount) * $user->settings->tax_value /100;
        }

        //check if bill under channel
        if(isset($bill->application->channel_id)){
            $bill->channel_extra_amount = $request->channel_extra_amount;
            $bill->channel_extra_title = $request->channel_extra_title;
            if($bill->add_tax){
                $bill->channel_extra_vat = $bill->channel_extra_amount * $bill->tax_value / 100;
            }
        }

        $bill->discount = $discount;
        $bill->vat = $vat;
        $bill->number = $bill->getNumber();
        $bill->sub_total = $sub_total;
        $bill->total = $sub_total - $discount + $vat + $bill->channel_extra_amount + $bill->channel_extra_vat;
        $bill->status = 'pending';
        $bill->save();

        if(isset($request->tags)){
            $tags = explode(',', $request->tags);
            foreach($tags as $name){
                $tag = Tag::firstOrCreate(['name' => $name]);
                $tag->bills()->attach($bill);
            }
        }

        event(new BillCreated($bill));
        
        return new BillApiResource($bill);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function wordpress(Request $request)
    {
        $application = $request->application;
        $user = $application->user ?? null;

        $mobile = ltrim($request->customer_mobile, '+966');
        $mobile = ltrim($mobile, '966');
        $mobile = (int) $mobile;
        $request->merge(['customer_mobile'=> $mobile]);
        $validator = Validator::make($request->all(), [
            'customer_mobile' => ['required', 'regex:/(^[5]{1}[0-9]{8}$)/'],
            'customer_name' => ['required'],
            'customer_email' => ['required'],
        ]);

        if ($validator->fails()){
             return view('bills.error', ['error' => $validator->errors()->first()]);
        }

        Bill::where('reference_id', $request->reference_id)
            ->where('user_id', $application->user_id ?? null)
            ->where('status', 'pending')
            ->update(['status' => 'canceled']);

        $customer = Customer::updateOrCreate([
            'user_id' => $user->id,
            'mobile' => $request->customer_mobile,
        ],[
            'name' => $request->customer_name, 
            'email' => $request->customer_email,
        ]);

        $bill = Bill::create([
            'user_id' => $user->id,
            'status' => 'pending',
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

            'add_discount' => (isset($request->add_discount) && ($request->add_discount == 'on' || $request->add_discount == true) )? true : false,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,

            'add_tax' => $request->add_tax ?? false,
            'tax_name' => $request->tax_name,
            'tax_value' => $request->tax_value,

            'send_sms' =>  (isset($request->send_sms) && ($request->send_sms == 'on' || $request->send_sms == true) )? true : false,
            'send_email' => (isset($request->send_email) && ($request->send_email == 'on' || $request->send_email == true) )? true : false,
            'reference_id' => $request->reference_id,
        ]);

        if(isset($user->settings->create_send_sms)){
            $bill->send_sms = $user->settings->create_send_sms;
        }
        if(isset($user->settings->create_send_email)){
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
        if($request->add_discount || $request->add_discount == 'on'){
            switch ($request->discount_type) {
                case 'fixed':
                    $discount = $request->discount_value;
                    break;
                case 'percentage':
                    $discount = $sub_total * $request->discount_value / 100;
                    break;
            }
        }

        if($request->add_tax ){
            $bill->add_tax = $request->add_tax;
            $bill->tax_value = $request->tax_value;
        }elseif($user->settings->add_tax){
            $bill->add_tax = $user->settings->add_tax;
            $bill->tax_value = $user->settings->tax_value;      
        }

        if($request->add_tax){
           $vat = ($sub_total -$discount) * $request->tax_value /100;
        }elseif($user->settings->add_tax){
            $vat = ($sub_total -$discount) * $user->settings->tax_value /100;
        }

        $bill->discount = $discount;
        $bill->vat = $vat;
        $bill->number = $bill->getNumber();
        $bill->sub_total = $sub_total;
        $bill->total = $sub_total - $discount + $vat;
        $bill->save();
        
        event(new BillCreated($bill));

        return redirect($bill->pay_url);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show($id, CheckBillApiRequest $request)
    {
        $application = $request->application;

        $bill = Bill::find($id);
        if(!isset($bill)){
           return response()->json([
                "message" => "The given data was invalid.",
                'errors' => [
                    'credential' =>[__("can't find this record in database.")] 
                ] 
           ], 422);
        }

        if(isset($application) && $application->user_id == $bill->user_id){
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
        if(!isset($application)){
           return response()->json([
                "message" => "The given data was invalid.",
                'errors' => [
                    'credential' =>[__('application_id or application_secret is not coreect')] 
                ] 
           ], 422);
        }

        $bill = Bill::find($id);
        if(!isset($bill)){
           return response()->json([
                "message" => "The given data was invalid.",
                'errors' => [
                    'credential' =>[__("can't find this record in database.")] 
                ] 
           ], 422);
        }

        if(isset($application) && $application->user_id == $bill->user_id){
            if($bill->status != 'canceled' && $bill->status != 'paid'){
                $bill->status = 'canceled';
                $bill->save();
                event( new BillStatusUpdated($bill) );
            }

            return new BillResource($bill);
        }else{
           return response()->json([
                "message" => "The given data was invalid.",
                'errors' => [
                    'credential' =>[__("your application not match bill's application")] 
                ] 
           ], 422);
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
        if(!isset($application)){
           return response()->json([
                "message" => "The given data was invalid.",
                'errors' => [
                    'credential' =>[__('application_id or application_secret is not coreect')] 
                ] 
           ], 422);
        }


        $bill = Bill::find($id);
        if(!isset($bill)){
           return response()->json([
                "message" => "The given data was invalid.",
                'errors' => [
                    'credential' =>[__("can't find this record in database.")] 
                ] 
           ], 422);
        }

        if(isset($application) && $application->user_id == $bill->user_id){
            if($bill->status != 'expired' && $bill->status != 'paid'){
                $bill->status = 'expired';
                $bill->save();
                event( new BillStatusUpdated($bill) );
            }
            return new BillResource($bill);
        }else{
            return response()->json(['success' => false]);
        }
    }

    /**
     * get Application.
     *
     * @return \App\Models\Application
     */
    public function getApplication($application, $request)
    {
        $user = $application->user;
        $application = $user->applications()->where('name', $request->application_name)->first();
        if(!isset($application)){
            if($request->application_name == 'payment_links'){
                $application = new Application;
                $application->user_id = $user->id;
                $application->name = $request->application_name;
                $application->redirect = $request->redirect_url;
                $application->webhook_url = $request->webhook_url;
                $application->fail_redirect_url = '';
                $application->secret = Str::random(20);
                $application->webhook_secret = Str::random(20);
                $application->save();      
            }
        }
        return $application;
    }


    /**
     * refund Bill.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function refund($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', 'in:partial_refund,total_refund'],
            'amount' => [
                'nullable', 
                'required_if:type,partial_refund', 
                new AmountPartialRefund($id), 
                'numeric', 'gt:0', new AmountPartialRefundGTBalance($id)
            ],
        ]);

        $bill = Bill::find($id);

        $validator->after(function ($validator) use($bill){
            $otherDate = Carbon::now()->subDays(14);

            if ($otherDate->gt($bill->paid_at)) {
                $validator->errors()->add('bill', __('It must not pass more than 14 days on the date of payment of the Bill'));
            }
        });

        if ($validator->fails())
        {
            return response()->json(['error' => $validator->errors()], 400);
        }

        if($request->type == 'partial_refund'){
            $bill->setPartialRefunded($request->amount);
        } else if ($bill->is_able_total_refund){
            if($bill->setRefunded()){
            }
        }else{
            return response()->json(['error' => [
                'refund' => __("Quantity must be less than or equal to the user's balance")
            ]], 400);
        }
        return new BillResource($bill);
    }
}
