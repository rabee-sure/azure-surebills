<?php

namespace App\Http\Controllers;

use App\Bill;
use App\BillItem;
use App\Customer;
use App\Events\BillCreated;
use App\Events\BillPaid;
use App\Events\BillStatusUpdated;
use App\Exceptions\ValidationException;
use App\Helpers\PaymentHelper as HelpersPaymentHelper;
use App\Http\Requests\BillRequest;
use App\Http\Requests\PayBillRequest;
use App\PaymentLog;
use App\Payment\Facades\Payment;
use App\Payment\Invoice;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException as ValidationsException;
use PaymentHelper;
use Log;
use IlluminateSupportFacadesLog;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //  private $invoice;
    //  public function __construct(Invoice $invoice)
    //  {
    //      $this->invoice($invoice);
    //  }

    public function index(Request $request)
    {
        $date_start = $request->date_start ?? null;
        $date_to = $request->date_to ?? null;

        if(!$request->dont_update_statuses){
            session(['status_filters'=> $request->statuses]);
        }

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
        $bill = DB::transaction(function() use($request){
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
            if($bill->total <= 0){
                throw ValidationsException::withMessages(['total' => __('The total must be greater than 0')]);
            }
            $bill->save();
            return $bill;
        });

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

        if($bill->is_expired && $bill->status != 'paid' && $bill->status != 'canceled'){
            $bill->status = 'expired';
            $bill->save();
            event( new BillStatusUpdated($bill) );
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

        return PaymentHelper::checkPaymentStatus($invoice, $payment, $bill);

        // return the view with errors
        // return redirect()->route('paybillpage', ['id' => $bill->pay_id])
        //     ->withErrors(['field_name' => $invoice->getDetail('description')]);
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

        if($bill->status != 'paid'){
            $bill->status = 'canceled';
            $bill->canceled_at = Carbon::now();
            $bill->save();
            event( new BillStatusUpdated($bill) );
        }

        return redirect()->back();
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function log(PaymentLog $log)
    {
        return view('bills.log', [
            'bill' => $log->bill,
            'log' => $log
        ]);
    }

    public function masterCardWebHookResponse(Request $request)
    {

        $response =  $arr = array (
            '3DSecure' =>
            array (
              'veResEnrolled' => 'N',
              'xid' => 'HS7DYyuWTF9UKrzXE3W/0JD55QQ=',
            ),
            '3DSecureId' => '6f6766f1-d6fd-4460-9287-d63526efbb4d',
            'customer' =>
            array (
              'firstName' => 'amr',
            ),
            'device' =>
            array (
              'browser' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36',
              'ipAddress' => '156.216.17.194',
            ),
            'gatewayEntryPoint' => 'CHECKOUT',
            'merchant' => 'TEST3000000330',
            'order' =>
            array (
              'amount' => 4.0,
              'chargeback' =>
              array (
                'amount' => 0,
                'currency' => 'SAR',
              ),
              'creationTime' => '2020-12-03T07:16:09.725Z',
              'currency' => 'SAR',
              'description' => 'Invoice number: 1000345',
              'id' => 'UVSZ-Z6RW-CZEN',
              'lastUpdatedTime' => '2020-12-03T07:16:09.738Z',
              'merchantAmount' => 4.0,
              'merchantCategoryCode' => '8999',
              'merchantCurrency' => 'SAR',
              'reference' => '1c3e9aff-7160-41b0-ac8f-7d642d25cb9f',
              'status' => 'FAILED',
              'totalAuthorizedAmount' => 0.0,
              'totalCapturedAmount' => 0.0,
              'totalRefundedAmount' => 0.0,
            ),
            'response' =>
            array (
              'gatewayCode' => 'BLOCKED',
            ),
            'result' => 'FAILURE',
            'risk' =>
            array (
              'response' =>
              array (
                'gatewayCode' => 'REJECTED',
                'review' =>
                array (
                  'decision' => 'NOT_REQUIRED',
                  'note' => NULL,
                ),
                'rule' =>
                array (
                  0 =>
                  array (
                    'data' => 'NO_LIABILITY_SHIFT',
                    'name' => 'MSO_3D_SECURE',
                    'recommendation' => 'REJECT',
                    'type' => 'MSO_RULE',
                  ),
                  1 =>
                  array (
                    'data' => '511111',
                    'name' => 'MSO_BIN_RANGE',
                    'recommendation' => 'NO_ACTION',
                    'type' => 'MSO_RULE',
                  ),
                  2 =>
                  array (
                    'data' => '156.216.17.194',
                    'name' => 'MSO_IP_ADDRESS_RANGE',
                    'recommendation' => 'NO_ACTION',
                    'type' => 'MSO_RULE',
                  ),
                  3 =>
                  array (
                    'data' => 'EGY',
                    'name' => 'MSO_IP_COUNTRY',
                    'recommendation' => 'NO_ACTION',
                    'type' => 'MSO_RULE',
                  ),
                ),
              ),
            ),
            'sourceOfFunds' =>
            array (
              'provided' =>
              array (
                'card' =>
                array (
                  'brand' => 'MASTERCARD',
                  'expiry' =>
                  array (
                    'month' => '4',
                    'year' => '27',
                  ),
                  'fundingMethod' => 'DEBIT',
                  'issuer' => 'FISERV SOLUTIONS, LLC',
                  'nameOnCard' => 'amr',
                  'number' => '511111xxxxxx1118',
                  'scheme' => 'MASTERCARD',
                  'storedOnFile' => 'NOT_STORED',
                ),
              ),
              'type' => 'CARD',
            ),
            'timeOfLastUpdate' => '2020-12-03T07:16:09.738Z',
            'timeOfRecord' => '2020-12-03T07:16:09.738Z',
            'transaction' =>
            array (
              'acquirer' =>
              array (
                'id' => 'RIYADBANK_S2I',
                'merchantId' => '3000000330',
              ),
              'amount' => 4.0,
              'currency' => 'SAR',
              'id' => '1',
              'source' => 'INTERNET',
              'stan' => '0',
              'type' => 'PAYMENT',
            ),
            'version' => '58',
        );


        $orderBody = json_decode(json_encode($response), FALSE);
        $notPaidBill = Bill::where([['id', $orderBody->order->reference], ['status', '<>', 'paid']])->first();

        if($notPaidBill)
        {
            $invoice = new Invoice();
            $details = $invoice->detail(['bill' => $notPaidBill->toArray()])->getDetails();
            PaymentHelper::handlePaymentResponse($invoice, $orderBody, $details, true);
        }
    }
}
