<?php

namespace App\Http\Controllers;

use App\Bill;
use App\BillItem;
use App\Customer;
use App\Events\BillCreated;
use App\Events\BillPaid;
use App\Events\BillStatusUpdated;
use App\Http\Requests\BillRequest;
use App\Http\Requests\PayBillRequest;
use App\PaymentLog;
use App\Payment\Facades\Payment;
use App\Payment\Invoice;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $date_start = $request->date_start ?? null;
        $date_to = $request->date_to ?? null;

        $bills = Bill::where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->when($request->statuses, function ($q) use($request){
                $q->whereIn('status', $request->statuses);
            })
            ->when($date_start, function($q) use($date_start, $date_to){
                $q->whereDate('created_at', '>=', Carbon::parse($date_start))
                    ->whereDate('created_at', '<=', Carbon::parse($date_to)) ;
            })
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
        $user = auth()->user();
        $customer = Customer::updateOrCreate([
            'mobile' => $request->customer_mobile,
            'user_id' => $user->id,
        ],[
            'name' => $request->customer_name, 
            'email' => $request->customer_email,
        ]);

        $bill = Bill::create([
            'user_id' => $user->id,
            'business_name' => $user->business_name,
            'customer_id' => $customer->id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_mobile' => $request->customer_mobile,
            'customer_notes' => $request->customer_notes,

            'expiry_date' => $request->expiry_date,
            'expiry_hours' => $request->expiry_hours ?? 0,
            'expiry_minutes' => $request->expiry_minutes ?? 0,
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
        $payment_fees = 0;

        if($user->pay_fees == "client"){
            $payment_fees = ($sub_total * ($user->credit_cards_percentage / 100)) + $user->credit_cards_fixed;
        }

        if($request->add_discount){
            switch ($request->discount_type) {
                case 'fixed':
                    $discount = $request->discount_value;
                    break;
                case 'percentage':
                    $discount = ($sub_total + $payment_fees) * $request->discount_value / 100;
                    break;
            }
        } 

        if($request->add_tax){
           $vat = ($sub_total + $payment_fees - $discount) * $request->tax_value /100;
        }

        $bill->payment_fees = $payment_fees;
        $bill->discount = $discount;
        $bill->vat = $vat;
        $bill->number = $bill->getNumber();
        $bill->sub_total = $sub_total;
        $bill->total = $sub_total + $payment_fees - $discount + $vat;
        $bill->save();
        
        event(new BillCreated($bill));
        return redirect()->route('bills.show', $bill);
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
        $bill = Bill::decodeId($id);

        if ($lang && in_array($lang, ['en', 'ar'])) {
            \App::setLocale($lang);
        }else{
           \App::setLocale($bill->user->settings->default_lang); 
        }

        if(!$bill){
            abort(404);
        }

        if($bill->is_invalid){
            return view('bills.status', ['bill' => $bill]);
        }

        $invoice = (new Invoice)->amount( number_format($bill->total, 2, '.', ''));
        $invoice->detail(['bill' => $bill->toArray()])
            ->detail(['hash' => $bill->pay_id]);
        
        $countdown = $bill->created_at
                ->addDays($bill->expiry_date)
                ->addMinutes($bill->expiry_minutes)
                ->addHours($bill->expiry_hours)
                ->format('m/d/Y H:i:s')
                ;
                // dd($countdown);
        return view('bills.pay', compact('bill', 'id', 'countdown'));
    }
    
    /**
     * Display the payment page for a specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function payment_iframe($id, $method, $locale = null)
    {
        $bill = Bill::find($id);

        $payment = PaymentLog::create([
            'bill_id'        => $bill->id,
            'payment_method' => $method,
            'results'        => [],
            'data'           => [],
            'status'         => 0,
        ]);

        $invoice = (new Invoice)->amount( number_format($bill->total, 2, '.', ''));
        $invoice->detail(['bill' => $bill->toArray()])
            ->detail(['surebills_payment_log_id' => $payment->id])
            ->detail(['hash' => $payment->hash_id])
            ->detail(['locale' => $locale ?? app()->getLocale()]);
        
        return $payment_iframe = Payment::via($payment->payment_method)->generateIframe($invoice);
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
        $payment = PaymentLog::decodeId($hash);
        $bill    = $payment->bill;

        if(!$payment || !$bill){
            abort(404);
        }

        if($bill->is_invalid){
            return view('bills.status', ['bill' => $bill]);
        }

        $invoice = (new Invoice)->amount( number_format($bill->total, 2, '.', ''))
            ->detail(['bill_id' => $bill->id])
            ->detail(['bill' => $bill->toArray()])
            ->detail(['payment_id' => $request->get('id')]);
        $invoice = Payment::via($payment->payment_method)->paymentStatus($invoice);


        // if success
        if($invoice->getDetail('success')){

            // log
            $payment->results = $invoice->getDetails();
            $payment->status = 1;
            $payment->save();
            
            $bill->paid();

            if($bill->application){
                $url = $bill->application->redirect.'?reference_id='.$bill->reference_id.'&status='.$bill->status.'&bill_id='.$bill->id.'&pay_url='.$bill->pay_url;
                return redirect($url);
            }
            return redirect()->route('paybillpage', ['id' => $bill->pay_id]);
        }

        // log for the payment
        $payment->results = $invoice->getDetails();
        $payment->status = 0;
        $payment->save();

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
        event( new BillStatusUpdated($bill) );

        return redirect()->back();
    }
}
