<?php

namespace App\Http\Controllers\Api;

use App\Events\BillCreated;
use App\Events\BillStatusUpdated;
use App\Helpers\BillSignatureHelper;
use App\Helpers\CybersourceMicroformHandlerHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\BillApiRequest;
use App\Http\Requests\CheckBillApiRequest;
use App\Http\Requests\DebitNoteApiRequest;
use App\Http\Requests\DebitNoteRequest;
use App\Http\Resources\BillApiResource;
use App\Http\Resources\BillResource;
use App\Models\Application;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Customer;
use App\Models\RefundedBill;
use App\Rules\AmountPartialRefund;
use App\Rules\AmountPartialRefundGTBalance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Tag;
use App\Payment\Invoice;
use phpDocumentor\Reflection\Types\Null_;

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
        $bill_user_id = $application->user_id ?? null;
        
        if($user && $user->store_main_user_id)
        {
            $user = $user->mainStoreUser;
            $bill_user_id = $user->id ?? null;
        }
        
        if($request->application_name){
            $application = $this->getApplication($application, $request);
        }

        Bill::where('reference_id', $request->reference_id)->where('user_id', $bill_user_id)->where('status', 'pending')->update(['status' => 'canceled']);

        // Find the customer by mobile or email
        $customer = Customer::where('user_id', $user->id)
                    ->where(function($query) use ($request) {
                        $query->where('mobile', $request->customer_mobile)
                              ->orWhere('email', $request->customer_email);
                    })
                    ->first();

        if ($customer) {
            // Update the existing customer
            $customer->update([
                'name' => $request->customer_name,
                'mobile' => $request->customer_mobile,
                'email' => $request->customer_email,
                'bullding_no' => $request->customer_bullding_no,
                'street_name' => $request->customer_street_name,
                'district' => $request->customer_district,
                'city' => $request->customer_city,
                'postal_code' => $request->customer_postal_code,
                'additional_no' => $request->customer_additional_no,
                'other_buyer_id' => $request->customer_other_buyer_id,
                'vat_registration_number' => $request->customer_vat_registration_number,
        ]);
        } else {
            // Create a new customer
            $customer = Customer::create([
                'name' => $request->customer_name,
                'mobile' => $request->customer_mobile,
                'email' => $request->customer_email,
                'bullding_no' => $request->customer_bullding_no,
                'street_name' => $request->customer_street_name,
                'district' => $request->customer_district,
                'city' => $request->customer_city,
                'postal_code' => $request->customer_postal_code,
                'additional_no' => $request->customer_additional_no,
                'other_buyer_id' => $request->customer_other_buyer_id,
                'vat_registration_number' => $request->customer_vat_registration_number,
                'user_id' => $user->id
            ]);
        }
       
        $send_sms = $request->send_sms ?? 0;
        $send_email = $request->send_email === null ? $user->settings->create_send_email : $send_email = $request->send_email;

        $bill = Bill::create([
            'user_id' => $user->id,
            'creted_by' => $user->id,
            'status' => 'pending',
            'application_id' => $application->id,

            'business_name' => $user->store_main_user_id ? $user->mainStoreUser->business_name : $user->business_name,
            'customer_id' => $customer->id,

            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_mobile' => $request->customer_mobile,
            'customer_notes' => $request->customer_notes,

            'expiry_date' => $request->expiry_date ?? 0,
            'expiry_hours' => $request->expiry_hours ?? 0,
            'expiry_minutes' => $request->expiry_minutes ?? 0,
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

            'source' => 'api',
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
        $bill->fixed_total = $sub_total - $discount + $vat + $bill->channel_extra_amount + $bill->channel_extra_vat;
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

    public function storeDebitNote($mainBillId, DebitNoteApiRequest $request){
        $application = $request->application;
        if($application->user){
            $user = $application->user->store_main_user_id ? $application->user->mainStoreUser : $application->user;
            $bill_user_id = $application->user->store_main_user_id ? $application->user->store_main_user_id : $application->user_id;
        }else{
            $user = null;
            $bill_user_id = null;
        }

        if($request->application_name){
            $application = $this->getApplication($application, $request);
        }

        Bill::where('reference_id', $request->reference_id)->where('user_id', $bill_user_id)->where('status', 'pending')->update(['status' => 'canceled']);

        $mainBill = Bill::find($mainBillId);

        if($mainBill->debit_note_bill_id != Null){
            return response()->json([
                "message" => "The given data was invalid.",
                'errors' => [
                    'authorization' =>[__("can't create debit note bill for other debit note bill")]
                ]
           ], 422);
        }

        if(!in_array($mainBill->status, ['paid', 'paid_cash', 'paid_bank_transfer', 'paid_machine'])){
            return response()->json([
                "message" => "The given data was invalid.",
                'errors' => [
                    'authorization' =>[__("can't create debit note bill for not paid bill")]
                ]
           ], 422);
        }

        $send_sms = 0;
        $send_email = $request->send_email === null ? $user->settings->create_send_email : $send_email = $request->send_email;

        $bill = Bill::create([
            'user_id' => $user->id,
            'creted_by' => $user->id,
            'status' => 'pending',
            'application_id' => $application->id,

            'business_name' => $mainBill->business_name,
            'customer_id' => $mainBill->customer_id,

            'customer_name' => $mainBill->customer_name,
            'customer_email' => $mainBill->customer_email,
            'customer_mobile' => $mainBill->customer_mobile,
            'customer_notes' => $mainBill->customer_notes,

            'expiry_date' => $request->expiry_date ?? 0,
            'expiry_hours' => $request->expiry_hours ?? 0,
            'expiry_minutes' => $request->expiry_minutes ?? 0,
            'due_date' => Carbon::parse($request->due_date),

            'add_discount' => $request->add_discount ?? false,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,

            'add_tax' => $mainBill->add_tax ?? false,
            'tax_name' => $mainBill->tax_name,
            'tax_value' => $mainBill->tax_value,

            'send_sms' => $send_sms,
            'send_email' => $send_email,
            'reference_id' => $request->reference_id,
            'is_redirect' => $request->is_redirect,

            'bill_redirect_url' => $request->redirect_url,
            'bill_webhook_url' => $request->webhook_url,

            'source' => 'api',
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

        if($mainBill->add_tax !== null){
            $bill->add_tax = $mainBill->add_tax;
            $bill->tax_value = $mainBill->tax_value;
            $vat = ($sub_total -$discount) * $mainBill->tax_value /100;

        }elseif($user->settings->add_tax){
            $bill->add_tax = $user->settings->add_tax;
            $bill->tax_value = $user->settings->tax_value;
            $vat = ($sub_total -$discount) * $user->settings->tax_value /100;
        }

        //check if bill under channel
        if(isset($bill->application->channel_id)){
            $bill->channel_extra_amount = $request->channel_extra_amount;
            $bill->channel_extra_title = $request->channel_extra_title;
            if($mainBill->add_tax){
                $bill->channel_extra_vat = $bill->channel_extra_amount * $mainBill->tax_value / 100;
            }
        }

        $bill->discount = $discount;
        $bill->vat = $vat;
        $bill->number = $bill->getNumber();
        $bill->sub_total = $sub_total;
        $bill->total = $sub_total - $discount + $vat + $bill->channel_extra_amount + $bill->channel_extra_vat;
        $bill->fixed_total = $sub_total - $discount + $vat + $bill->channel_extra_amount + $bill->channel_extra_vat;
        $bill->status = 'pending';
        $bill->debit_note_bill_id = $mainBill->id;
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
        if($application->user){
            $user = $application->user->store_main_user_id ? $application->user->mainStoreUser : $application->user;
            $bill_user_id = $application->user->store_main_user_id ? $application->user->store_main_user_id : $application->user_id;
        }else{
            $user = null;
            $bill_user_id = null;
        }

        $rules = [
            'customer_mobile' => ['required'],
            'customer_name' => ['required'],
            'customer_email' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){
             return view('bills.error', ['error' => $validator->errors()->first()]);
        }

        if(config('bills.pay_page_expiration_time_type') == 'Days')
        {
            if($request->expiry_date){
                if($request->expiry_date >= config('bills.pay_page_expiration_time')){
                    $expiry_date = config('bills.pay_page_expiration_time');
                    $expiry_hours = 0;
                    $expiry_minutes = 0;
                }
            }else{
                $expiry_date = 0;
            }
        }
        else if(config('bills.pay_page_expiration_time_type') == 'Hours')
        {
            $expiry_date = 0;
            if($request->expiry_hours){
                if($request->expiry_hours >= config('bills.pay_page_expiration_time')){
                    $expiry_hours = config('bills.pay_page_expiration_time');
                    $expiry_minutes = 0;
                }
            }else{
                $expiry_hours = 0;
            }
        }
        else if(config('bills.pay_page_expiration_time_type') == 'Minutes')
        {
            $expiry_date = 0;
            $expiry_hours = 0;
            if($request->expiry_minutes){
                if($request->expiry_minutes >= config('bills.pay_page_expiration_time')){
                    $expiry_minutes = config('bills.pay_page_expiration_time');
                }
            }else{
                $expiry_minutes = 0;
            }
        }

        Bill::where('reference_id', $request->reference_id)
            ->where('user_id', $bill_user_id)
            ->where('status', 'pending')
            ->update(['status' => 'canceled']);

        // Find the customer by mobile or email
        $customer = Customer::where('user_id', $user->id)
                    ->where(function($query) use ($request) {
                        $query->where('mobile', $request->customer_mobile)
                              ->orWhere('email', $request->customer_email);
                    })
                    ->first();

        if ($customer) {
            // Update the existing customer
            $customer->update([
                'name' => $request->customer_name,
                'mobile' => $request->customer_mobile,
                'email' => $request->customer_email,
                'bullding_no' => $request->customer_bullding_no,
                'street_name' => $request->customer_street_name,
                'district' => $request->customer_district,
                'city' => $request->customer_city,
                'postal_code' => $request->customer_postal_code,
                'additional_no' => $request->customer_additional_no,
                'other_buyer_id' => $request->customer_other_buyer_id,
                'vat_registration_number' => $request->customer_vat_registration_number,
        ]);
        } else {
            // Create a new customer
            $customer = Customer::create([
                'name' => $request->customer_name,
                'mobile' => $request->customer_mobile,
                'email' => $request->customer_email,
                'bullding_no' => $request->customer_bullding_no,
                'street_name' => $request->customer_street_name,
                'district' => $request->customer_district,
                'city' => $request->customer_city,
                'postal_code' => $request->customer_postal_code,
                'additional_no' => $request->customer_additional_no,
                'other_buyer_id' => $request->customer_other_buyer_id,
                'vat_registration_number' => $request->customer_vat_registration_number,
                'user_id' => $user->id
            ]);
        }

        $bill = Bill::create([
            'user_id' => $user->id,
            'created_by' => $user->id,
            'status' => 'pending',
            'application_id' => $application->id,

            'business_name' => $user->business_name,
            'customer_id' => $customer->id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_mobile' => $request->customer_mobile,
            'customer_notes' => $request->customer_notes,

            'expiry_date' => $expiry_date,
            'expiry_hours' => $expiry_hours,
            'expiry_minutes' => $expiry_minutes,
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

            'source' => 'api',
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
        $bill->fixed_total = $sub_total - $discount + $vat;
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

        if($bill->status != 'pending'){
           return response()->json([
                "message" => "The given data was invalid.",
                'errors' => [
                    'credential' =>[__("can't cancel this bill")]
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

        if(!$bill->is_able_refund){
            return response()->json(['error' => [
                'refund' => __("You can't refund this bill now please try again later")
            ]], 400);
        }

        if($bill->debit_note_bill_id != null){
            return response()->json(['error' => [
                'refund' => __("You can't refund this Debit Note")
            ]], 400);
        }

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

        $method = $bill->getRefundedMethod();

        if($request->type == 'partial_refund'){
            if($bill->status == 'paid' && $request->amount > $bill->user->balance){
                return response()->json(['error' => [
                    'refund' => __("Quantity must be less than or equal to the user's balance")
                ]], 400);
            }
            $bill->setPartialRefunded($request->amount);

            $refundedBill = RefundedBill::create([
                'bill_id' => $bill->id,
                'user_id' => $bill->user_id,
                'amount' => $request->amount,
                'status' => 'cn_refunded',
                'method' => $method,
                'customer_name' => $bill->customer_name
            ]);

            $refundedBill->number = $refundedBill->getNumber();
            $refundedBill->save();

        } else {
            if ($bill->is_able_total_refund){
                $totalRefundAmountWithFees = $bill->due_to_client;
                $totalRefundAmountWithoutFees = $bill->total;

                if($bill->setRefunded()){
                    $refundedBill = RefundedBill::create([
                        'bill_id' => $bill->id,
                        'user_id' => $bill->user_id,
                        'amount' => $bill->user->able_refund_with_fees ? $totalRefundAmountWithFees : $totalRefundAmountWithoutFees,
                        'status' => 'cn_refunded',
                        'method' => $method,
                        'customer_name' => $bill->customer_name
                    ]);

                    $refundedBill->number = $refundedBill->getNumber();
                    $refundedBill->save();
                }
            }else{
                return response()->json(['error' => [
                    'refund' => __("Quantity must be less than or equal to the user's balance")
                ]], 400);
            }
        }

        return new BillResource($bill);
    }

    public function paymentForm(Request $request){
        $validator = Validator::make($request->all(), [
            'application_id' => ['required'],
            'application_secret' => ['required'],
            'bill_id' => ['required'],
            'lang' => ['required', 'in:en,ar'],
            'host' => ['required'],
        ]);

        $host = $request->host;
        $id = $request->bill_id;
        $lang = $request->lang;

        $bill = Bill::find($id);

        // Prevent access if bill already paid
        if ($bill->status != 'pending') {
            return response()->json(['error' => [
                'bill' => __('This bill not pending you can not access it')
            ]], 403);
        }

        // Prevent access if bill is older than pay page expiration time
        if(config('bills.pay_page_expiration_time_type') == 'Days')
        {
            if ($bill->created_at->lt(now()->subDays(config('bills.pay_page_expiration_time')))) {
                return response()->json(['error' => [
                    'bill' => __('This payment link has expired.')
                ]], 403);
            }
        }
        else if(config('bills.pay_page_expiration_time_type') == 'Hours')
        {
            if ($bill->created_at->lt(now()->subHours(config('bills.pay_page_expiration_time')))) {
                return response()->json(['error' => [
                    'bill' => __('This payment link has expired.')
                ]], 403);
            }
        }
        else if(config('bills.pay_page_expiration_time_type') == 'Minutes')
        {
            if ($bill->created_at->lt(now()->subMinutes(config('bills.pay_page_expiration_time')))) {
                return response()->json(['error' => [
                    'bill' => __('This payment link has expired.')
                ]], 403);
            }
        }

        if ($lang && in_array($lang, ['en', 'ar'])) {
            \App::setLocale($lang);
        } else {
            \App::setLocale($bill->user->settings->default_lang);
        }

        if (!$bill) {
            return response()->json(['error' => [
                'bill' => __('Bill not found')
            ]], 400);
        }

        $billStatus = '';
        if($bill->status != 'paid' || $bill->status != 'paid_cash' || $bill->status != 'paid_bank_transfer' || $bill->status != 'paid_machine'){
            $billStatus = 'paid';
        }

        if ($bill->is_expired && $billStatus != 'paid' && $bill->status != 'canceled') {
            $bill->status = 'expired';
            $bill->save();
            event(new BillStatusUpdated($bill));
        }

        if ($bill->is_invalid) {
            return response()->json(['view' => view('bills.status', compact('bill'))->render()]);
        }

        $invoice = (new Invoice)->amount(number_format($bill->total, 2, '.', ''));
        $invoice->detail(['bill' => $bill->toArray()])
            ->detail(['hash' => $bill->pay_id]);

        $countdown = $bill->created_at
            ->addDays($bill->expiry_date)
            ->addMinutes($bill->expiry_minutes)
            ->addHours($bill->expiry_hours)
            ->format('m/d/Y H:i:s');

        $years = [];
        $sureEasyRendrer = true;
        $microformSessionToken = $billSignature = $payTime = null;
        if(config('payment.default_payment_gateway') == 'cybersource')
        {
            $years = range(date('Y'), date('Y') + 10);
            // $microformSessionToken = CybersourceMicroformHandlerHelper::retrieveMicroformToken($request->host);
            $payTime = now()->unix();
            $billSignature = BillSignatureHelper::generateSignature($bill, $payTime);
            $payForm = 'bills.cybersource_pay_form';
        }
        else
        {
            $payForm = 'bills.mastercard_pay_form';
        }
        if ($bill->application_id == null || !$bill->user->settings->api_bill_style) {
            return response()->json(['view' => view('bills.pay', compact('host', 'bill', 'id', 'countdown', 'sureEasyRendrer', 'payForm', 'years', 'microformSessionToken', 'billSignature', 'payTime'))->render()]);
        }
        
        return response()->json(['view' => view('bills.payment_page', compact('host', 'bill', 'id', 'countdown', 'sureEasyRendrer', 'payForm', 'years', 'microformSessionToken', 'billSignature', 'payTime'))->render()]); 
    }
}
