<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use PaymentHelper;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Customer;
use App\Payment\Invoice;
use App\Models\PaymentLog;
use App\Events\BillCreated;
use Illuminate\Http\Request;
use App\Payment\Facades\Payment;
use App\Events\BillStatusUpdated;
use App\Http\Requests\BillRequest;
use Illuminate\Support\Facades\DB;
use App\Services\MasterCardService;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\RefundRequest;
use App\Models\RefundedBill;
use App\Models\Settings;
use Illuminate\Validation\ValidationException as ValidationsException;

class BillController extends Controller
{
    private $masterCardService;

    public function __construct()
    {
        $this->middleware('permission:show bills', ['only' => ['index','show']]);
        $this->middleware('permission:create bills', ['only' => ['create','store']]);
        $this->middleware('permission:change bill status', ['only' => ['changeStatus']]);
        $this->middleware('permission:refund bill', ['only' => ['refund']]);
        $this->middleware('permission:cancel bill', ['only' => ['cancel']]);

        $this->masterCardService = new MasterCardService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $date_start = $request->date_start ?? null;
        $date_to = $request->date_to ?? null;

        if (!$request->dont_update_statuses) {
            session(['status_filters' => $request->statuses]);
        }

        $statuses = array();
        if($request->statuses){
            $statuses = $request->statuses;
            $statuses = in_array('paid', $statuses) ? array_merge($statuses, ['paid_cash', 'paid_bank_transfer', 'refunded_cash', 'refunded_bank_transfer', 'refunded']) : $statuses;
            // $statuses = in_array('refunded', $statuses) ? array_merge($statuses, ['refunded_cash', 'refunded_bank_transfer']) : $statuses;
        }

        $bills = Bill::userId(auth()->user()->store_main_user_id ?? auth()->user()->id)
            ->when($statuses, function ($q) use ($statuses) {
                $q->whereIn('status', $statuses);
            })
            ->when($request->keyword, function ($q) use ($request) {
                $q->whereLike(['customer_name', 'number', 'user.name', 'user.business_name_en', 'user.business_name_ar'], $request->keyword);
            })
            ->when($date_start, function ($q) use ($date_start, $date_to) {
                $q->whereDate('created_at', '>=', Carbon::parse($date_start))
                    ->whereDate('created_at', '<=', Carbon::parse($date_to));
            })
            ->select('id', 'number', 'customer_name', 'sub_total', 'vat', 'discount', 'status', 'created_at', DB::raw("'bills' as model"));

        $refundedBills = RefundedBill::userId(auth()->user()->store_main_user_id ?? auth()->user()->id)
        ->when($statuses, function ($q) use ($statuses) {
            $q->whereIn('status', $statuses);
        })
        ->when($request->keyword, function ($q) use ($request) {
            $q->whereLike(['number'], str_replace("CN", "", $request->keyword));
        })
        ->when($date_start, function ($q) use ($date_start, $date_to) {
            $q->whereDate('created_at', '>=', Carbon::parse($date_start))
                ->whereDate('created_at', '<=', Carbon::parse($date_to));
        })
        ->select('id', DB::raw("CONCAT('CN', number) as number"), DB::raw("'Credit Note' as customer_name"), 'amount as sub_total', DB::raw("'0' as vat"), DB::raw("'0' as discount"), 'status', 'created_at', DB::raw("'refundedbills' as model"));

        $mergedBills = $bills->union($refundedBills)->orderBy('created_at', 'desc')->paginate($request->get('per_page', 10));

        return view('bills.index', ['bills' => $mergedBills]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if((!auth()->user()->mainStoreUser && count(auth()->user()->channels) > 0) || (auth()->user()->mainStoreUser && count(auth()->user()->mainStoreUser->channels) > 0))
        {
            abort(403);
        }
        $settings = Settings::userId(auth()->user()->store_main_user_id ?? auth()->user()->id)->first();
        return view('bills.create', compact('settings'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BillRequest $request)
    {
        if((!auth()->user()->mainStoreUser && count(auth()->user()->channels) > 0) || (auth()->user()->mainStoreUser && count(auth()->user()->mainStoreUser->channels) > 0))
        {
            abort(403);
        }

        $bill = DB::transaction(function () use ($request) {
            $user = auth()->user();

            $customer = Customer::updateOrCreate([
                'mobile' => $request->customer_mobile,
                'user_id' => $user->store_main_user_id ?? $user->id,
            ], [
                'name' => $request->customer_name,
                'email' => $request->customer_email,
                'bullding_no' => $request->bullding_no,
                'street_name' => $request->street_name,
                'district' => $request->district,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'additional_no' => $request->additional_no,
                'other_buyer_id' => $request->other_buyer_id,
                'vat_registration_number' => $request->vat_registration_number,
            ]);

            $bill = Bill::create([
                'user_id' => $user->store_main_user_id ?? $user->id,
                'created_by' => $user->id,
                'status' => 'pending',
                'business_name' => $user->business_name,
                'customer_id' => $customer->id,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_mobile' => $request->customer_mobile,
                'customer_notes' => $request->customer_notes,

                'expiry_date' => $request->expiry_date,
                'expiry_hours' => $request->expiry_hours ?? 0,
                'expiry_minutes' => $request->expiry_minutes ?? 0,
                'due_date' => date('Y-m-d', strtotime(str_replace('/', '-', $request->due_date))),

                'add_discount' => $request->add_discount,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,

                'add_tax' => $request->add_tax,
                'tax_name' => $request->tax_name,
                'tax_value' => $request->tax_value,

                'send_sms' => $request->send_sms,
                'send_email' => $request->send_email,
                
                'source' => 'sure_bill',
            ]);

            foreach ($request->items as $item) {
                BillItem::create([
                    'bill_id' => $bill->id,
                    'product_name' => $item['name'],
                    'product_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['quantity'] * $item['price'],
                ]);
            }

            $sub_total = $bill->items->sum('total');
            $discount = 0;
            $vat = 0;
            $payment_fees = 0;

            // not found in database
            if ($user->pay_fees == "client") {
                $payment_fees = ($sub_total * ($user->credit_cards_percentage / 100)) + $user->credit_cards_fixed;
            }

            if ($request->add_discount) {
                switch ($request->discount_type) {
                    case 'fixed':
                        $discount = $request->discount_value;
                        break;
                    case 'percentage':
                        $discount = ($sub_total + $payment_fees) * $request->discount_value / 100;
                        break;
                }
            }

            if ($request->add_tax) {
                $vat = ($sub_total + $payment_fees - $discount) * $request->tax_value / 100;
            }

            $bill->payment_fees = $payment_fees;
            $bill->discount = $discount;
            $bill->vat = $vat;
            $bill->number = $bill->getNumber();
            $bill->sub_total = $sub_total;
            $bill->total = $sub_total + $payment_fees - $discount + $vat;
            if ($bill->total <= 0) {
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
        // dd([
        //     'bill_' => $bill->due_to_client,
        //     'balance' => auth()->user()->balance,
        //     'if' => $bill->is_able_total_refund,
        // ]);
        // dd($bill)
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
        } else {
            \App::setLocale($bill->user->settings->default_lang);
        }

        if (!$bill) {
            abort(404);
        }

        $billStatus = '';
        if($bill->status != 'paid' || $bill->status != 'paid_cash' || $bill->status != 'paid_bank_transfer'){
            $billStatus = 'paid';
        }

        if ($bill->is_expired && $billStatus != 'paid' && $bill->status != 'canceled') {
            $bill->status = 'expired';
            $bill->save();
            event(new BillStatusUpdated($bill));
        }

        if ($bill->is_invalid) {
            return view('bills.status', ['bill' => $bill]);
        }

        $invoice = (new Invoice)->amount(number_format($bill->total, 2, '.', ''));
        $invoice->detail(['bill' => $bill->toArray()])
            ->detail(['hash' => $bill->pay_id]);

        $countdown = $bill->created_at
            ->addDays($bill->expiry_date)
            ->addMinutes($bill->expiry_minutes)
            ->addHours($bill->expiry_hours)
            ->format('m/d/Y H:i:s');

        if ($bill->application_id == null || !$bill->user->settings->api_bill_style) {
            return view('bills.pay', compact('bill', 'id', 'countdown'));
        }

        return view('bills.payment_page', compact('bill', 'id', 'countdown'));
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

        $invoice = (new Invoice)->amount(number_format($bill->total, 2, '.', ''));
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

        if (!$payment || !$bill) {
            abort(404);
        }

        if ($bill->is_invalid) {
            return view('bills.status', ['bill' => $bill]);
        }
        // dd($payment);

        $invoice = (new Invoice)->amount(number_format($bill->total, 2, '.', ''))
            ->detail(['bill_id' => $bill->id])
            ->detail(['bill' => $bill->toArray()])
            ->detail(['payment_id' => $request->get('id')]);
        $invoice = Payment::via($payment->payment_method)->paymentStatus($invoice);

        return PaymentHelper::checkPaymentStatus($invoice, $payment, $bill);
    }

    /**
     * Cancel Bill.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cancel($id, Request $request)
    {
        $bill = Bill::find($id);

        if ($bill->status != 'paid') {
            $bill->status = 'canceled';
            $bill->canceled_at = Carbon::now();
            $bill->save();
            event(new BillStatusUpdated($bill));
        }

        return redirect()->back();
    }

    /**
     * Cancel Bill.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changeStatus($id, Request $request)
    {
        $bill = Bill::find($id);

        if ($bill->status == 'pending') {
            $bill->status = $request->status;
            $bill->paid_at = Carbon::now();
            $bill->save();
            event(new BillStatusUpdated($bill));
        }

        return redirect()->back();
    }

    /**
     * refund Bill.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function refund($id, RefundRequest $request)
    {

        $bill = Bill::find($id);

        if ($request->type == 'partial_refund') {
            $bill->setPartialRefunded($request->amount);
            
            $refundedBill = RefundedBill::create([
                'bill_id' => $bill->id,
                'user_id' => $bill->user_id,
                'amount' => $request->amount,
            ]);
    
            $refundedBill->number = $refundedBill->getNumber();
            $refundedBill->save();

        } else if ($bill->is_able_total_refund) {
            if ($bill->setRefunded()) {

                $refundedBill = RefundedBill::create([
                    'bill_id' => $bill->id,
                    'user_id' => $bill->user_id,
                    'amount' => $bill->total,
                ]);
        
                $refundedBill->number = $refundedBill->getNumber();
                $refundedBill->save();

                return redirect()->back();
            }
        } else {
            return redirect()->back()->withErrors(['refund' => __("Quantity must be less than or equal to the user's balance")]);
        }

        return redirect()->back()->withErrors(['refund' => session('refund_error')]);
    }


    public function masterCardWebHookResponse(Request $request)
    {
        return $this->masterCardService->handleWebhook($request);
    }

    /**
     * invoice.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function invoice($id, $lang = null)
    {
        $bill = Bill::decodeId($id);
        return view('bills.invoice', compact('bill', 'id'));
    }

    public function billPrint($id, Request $request)
    {
        $bill = Bill::find($id);
        $type = $request->input('type');
        $lang = $request->input('lang');
        if($type == 'billA4' && $lang == 'en'){
          return view('bills.print_template.a4_en', compact('bill', 'lang'));
        }elseif($type == 'billA4' && $lang == 'ar'){
          return view('bills.print_template.a4_ar', compact('bill', 'lang'));
        }elseif($type == 'billTh' && $lang == 'en'){
          return view('bills.print_template.th_en', compact('bill', 'lang'));
        }elseif($type == 'billTh' && $lang == 'ar'){
          return view('bills.print_template.th_ar', compact('bill', 'lang'));
        }
    }
}
