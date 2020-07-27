<?php

namespace App\Http\Controllers;

use App\Bill;
use App\BillItem;
use App\Customer;
use Carbon\Carbon;
use App\PaymentLog;
use App\Transaction;
use App\Events\BillPaid;
use App\Payment\Invoice;
use App\Events\BillCreated;
use Illuminate\Http\Request;
use App\Payment\Facades\Payment;
use App\Http\Requests\BillRequest;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\PayBillRequest;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $bills = Bill::where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));
        return view('bills.index', ['bills' => $bills]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('bills.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BillRequest $request)
    {
        $customer = Customer::updateOrCreate([
            'mobile' => $request->customer_mobile,
        ],[
            'name' => $request->customer_name, 
            'email' => $request->customer_email,
            'user_id' => auth()->user()->id,
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
        return redirect()->route('bills.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Bill $bill)
    {
        return view('bills.show', ['bill' => $bill]);
    }

    /**
     * Display the payment page for a specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function pay($id, $lang = null)
    {
        if ($lang && in_array($lang, ['en', 'ar'])) {
            \App::setLocale($lang);
        }

        $bill = Bill::decodeId($id);

        if(!$bill){
            abort(404);
        }

        if($bill->is_invalid){
            return view('bills.status', ['bill' => $bill]);
        }

        // $invoice = (new Invoice)->amount( number_format($bill->total, 2, '.', ''));
        // $invoice->detail(['bill' => $bill->toArray()])
        //     ->detail(['hash' => $bill->pay_id]);
        // $payment_iframe = Payment::generateIframe($invoice);
        
        return view('bills.pay', compact('bill', 'id'));
    }
    
    /**
     * Display the payment page for a specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function payment_iframe($id)
    {
        $bill = Bill::find($id);
        $invoice = (new Invoice)->amount( number_format($bill->total, 2, '.', ''));
        $invoice->detail(['bill' => $bill->toArray()])
            ->detail(['hash' => $bill->pay_id]);
        return $payment_iframe = Payment::generateIframe($invoice);
        
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function postPay($id, PayBillRequest $request)
    {
        $bill = Bill::decodeId($id);

        $expiry = $request->get('expiry', '03/21');
        $expiry_array = str_replace(' ', '', explode("/", $expiry));
        $credit_card = str_replace(' ', '', $request->get('number', '4242424242424242'));
        // dd($this->validatecard($credit_card));
        $invoice = (new Invoice)->amount( number_format($bill->total, 2, '.', ''));
        $invoice->detail(['name' => $request->get('name')])
                ->detail(['number' => $credit_card])
                ->detail(['expiry' => $expiry])
                ->detail(['expiry_month' => $expiry_array[0]])
                ->detail(['expiry_year' => '20'.$expiry_array[1]])
                ->detail(['bill' => $bill->toArray()])
                ->detail(['cvc' => $request->get('cvc')]);
        // Purchase the given invoice.
        Payment::purchase($invoice, function($driver, $result){

        });

        // if success
        if($invoice->getDetail('success')){

            PaymentLog::create([
                'results' => $invoice->getDetails(),
                'data' => [],
                'status' => 1,
            ]);
            
            $bill->paid();

            if($bill->application){
                $url = $bill->application->redirect.'?reference_id='.$bill->reference_id.'&status='.$bill->status.'&bill_id='.$bill->id;
                return redirect($url);
            }

            return redirect()->route('paybillpage', ['id' => $bill->pay_id]);
        }

        // if pending redirect to complete
        if($invoice->getDetail('pending')){
            return redirect($invoice->getDetail('redirect')->url);
        }

        // create a log for the payment
        PaymentLog::create([
            'results' => $invoice->getDetails(),
            'data' => [],
            'status' => 0,
        ]);

        // return the view with errors
        return back()->withInput()->withErrors(['field_name' => $invoice->getDetail('result_description')]);
    }

    /**
     * Handle payment the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function handlePayment(Request $request, $hash)
    {
        $bill = Bill::decodeId($hash);

        if($bill == null || $bill->is_invalid){
            return view('bills.status', ['bill' => $bill]);
            // abort(404);
        }

        $invoice = (new Invoice)->amount( number_format($bill->total, 2, '.', ''))
            ->detail(['bill_id' => $bill->id])
            ->detail(['bill' => $bill->toArray()])
            ->detail(['payment_id' => $request->get('id')]);
        $invoice = Payment::paymentStatus($invoice);


        // if success
        if($invoice->getDetail('success')){

            $payment = PaymentLog::create([
                'bill_id' => $bill->id,
                'results' => $invoice->getDetails(),
                'data' => [],
                'status' => 1,
            ]);
            
            $bill->paid();

            if($bill->application){
                $url = $bill->application->redirect.'?reference_id='.$bill->reference_id.'&status='.$bill->status.'&bill_id='.$bill->id;
                return redirect($url);
            }
            return redirect()->route('paybillpage', ['id' => $bill->pay_id]);
        }

        // create a log for the payment
        PaymentLog::create([
            'bill_id' => $bill->id,
            'results' => $invoice->getDetails(),
            'data' => [],
            'status' => 0,
        ]);

        // return the view with errors
        return redirect()->route('paybillpage', ['id' => $bill->pay_id])
            ->withErrors(['field_name' => $invoice->getDetail('description')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        $bill = Bill::find($id);
        $bill->status = 'canceled';
        $bill->save();

        return redirect()->back();
    }
}
