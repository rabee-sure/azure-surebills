<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Helpers\PaymentHelper;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Customer;
use App\Payment\Invoice;
use App\Models\PaymentLog;
use App\Events\BillCreated;
use Illuminate\Http\Request;
use App\Payment\Facades\Payment;
use App\Events\BillStatusUpdated;
use App\Events\RequestMerchantBillsExport;
use App\Helpers\BillSignatureHelper;
use App\Helpers\CybersourceMicroformHandlerHelper;
use App\Http\Requests\BillRequest;
use App\Http\Requests\DebitNoteRequest;
use Illuminate\Support\Facades\DB;
use App\Services\MasterCardService;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\RefundRequest;
use App\Jobs\ExportMerchantBills;
use App\Models\RefundedBill;
use App\Models\Settings;
use Illuminate\Validation\ValidationException as ValidationsException;

class BillController extends Controller
{
    private $masterCardService;

    public function __construct()
    {
        $this->middleware('permission:show bills', ['only' => ['index','show']]);
        $this->middleware(['permission:create bills', 'verified.user'], ['only' => ['create','store',]]);
        $this->middleware(['permission:create debit note', 'verified.user'], ['only' => ['createDebitNote', 'storeDebitNote']]);
        $this->middleware('permission:change bill status', ['only' => ['changeStatus']]);
        $this->middleware(['permission:refund bill', 'verified.user'], ['only' => ['refund']]);
        $this->middleware(['permission:cancel bill', 'verified.user'], ['only' => ['cancel']]);

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
            $statuses = in_array('paid', $statuses) ? array_merge($statuses, ['paid_cash', 'paid_bank_transfer', 'paid_machine', 'refunded_cash', 'refunded_bank_transfer', 'refunded', 'refunded_machine']) : $statuses;
            // $statuses = in_array('refunded', $statuses) ? array_merge($statuses, ['refunded_cash', 'refunded_bank_transfer']) : $statuses;
        }

        $bills = Bill::userId(auth()->user()->store_main_user_id ?? auth()->user()->id)
            ->when($statuses, function ($q) use ($statuses) {
                $q->whereIn('status', $statuses);
            })
            ->when($request->keyword, function ($q) use ($request) {
                foreach (explode( ' - ',trim(str_replace(["DN", __('Bill')], "", $request->keyword))) as $item){
                    $q = $q->whereLike(['customer_name', 'number', 'user.name', 'user.business_name_en', 'user.business_name_ar'], '%'. trim($item) . '%');
                }
            })
            ->when($date_start, function ($q) use ($date_start, $date_to) {
                $q->whereDate('created_at', '>=', Carbon::parse($date_start))
                    ->whereDate('created_at', '<=', Carbon::parse($date_to));
            })
            ->select('id', DB::raw("(CASE WHEN debit_note_bill_id IS NULL THEN number ELSE CONCAT('DN', number) END) AS number"), 'customer_name', 'sub_total', 'vat', 'discount', 'status', DB::raw("'null' as method"),'created_at', DB::raw("'bills' as model"), 'debit_note_bill_id');

        $refundedBills = RefundedBill::userId(auth()->user()->store_main_user_id ?? auth()->user()->id)
        ->when($statuses, function ($q) use ($statuses) {
            $q->whereIn('status', $statuses);
        })
        ->when($request->keyword, function ($q) use ($request) {
            foreach (explode( ' - ',trim(str_replace(["CN", __('Bill')], "", $request->keyword))) as $item){
                $q = $q->whereLike(['customer_name', 'number'], '%'. trim($item) . '%');
            }
        })
        ->when($date_start, function ($q) use ($date_start, $date_to) {
            $q->whereDate('created_at', '>=', Carbon::parse($date_start))
                ->whereDate('created_at', '<=', Carbon::parse($date_to));
        })
        ->select('id', DB::raw("CONCAT('CN', number) as number"), 'customer_name', 'amount as sub_total', DB::raw("'0' as vat"), DB::raw("'0' as discount"), 'status', 'method', 'created_at', DB::raw("'refundedbills' as model"), DB::raw("'' as debit_note_bill_id"));

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

    public function createDebitNote($bill_id){
        if((!auth()->user()->mainStoreUser && count(auth()->user()->channels) > 0) || (auth()->user()->mainStoreUser && count(auth()->user()->mainStoreUser->channels) > 0))
        {
            abort(403);
        }
        $bill = Bill::find($bill_id);
        $this->authorize('checkPermission', $bill);
        if($bill->debit_note_bill_id == null && in_array($bill->status, ['paid', 'paid_cash', 'paid_bank_transfer', 'paid_machine'])){
            $settings = Settings::userId($bill->user_id)->first();
            return view('bills.debit_notes.create', compact(['settings', 'bill']));
        }else{
            return redirect()->back()->withErrors(['authorization' => __("You can't create debit note for this bill")]);
        }
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
                'business_name' => $user->store_main_user_id ? $user->mainStoreUser->business_name : $user->business_name,
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
            $bill->fixed_total = $sub_total + $payment_fees - $discount + $vat;
            if ($bill->total <= 0) {
                throw ValidationsException::withMessages(['total' => __('The total must be greater than 0')]);
            }
            $bill->save();
            return $bill;
        });

        event(new BillCreated($bill));
        return redirect()->route('bills.show', $bill);
    }

    public function storeDebitNote(DebitNoteRequest $request){
        if((!auth()->user()->mainStoreUser && count(auth()->user()->channels) > 0) || (auth()->user()->mainStoreUser && count(auth()->user()->mainStoreUser->channels) > 0))
        {
            abort(403);
        }

        $mainBill = Bill::find($request->bill_id);
        $this->authorize('checkPermission', $mainBill);

        if($mainBill->debit_note_bill_id == null && in_array($mainBill->status, ['paid', 'paid_cash', 'paid_bank_transfer', 'paid_machine'])){
            $bill = DB::transaction(function () use ($request, $mainBill) {
                $user = $mainBill->user;

                $bill = Bill::create([
                    'user_id' => $user->store_main_user_id ?? $user->id,
                    'created_by' => $user->id,
                    'status' => 'pending',
                    'business_name' => $mainBill->business_name,
                    'customer_id' => $mainBill->customer_id,
                    'customer_name' => $mainBill->customer_name,
                    'customer_email' => $mainBill->customer_email,
                    'customer_mobile' => $mainBill->customer_mobile,
                    'customer_notes' => $request->customer_notes,

                    'expiry_date' => $request->expiry_date,
                    'expiry_hours' => $request->expiry_hours ?? 0,
                    'expiry_minutes' => $request->expiry_minutes ?? 0,
                    'due_date' => date('Y-m-d', strtotime(str_replace('/', '-', $request->due_date))),

                    'add_discount' => $request->add_discount,
                    'discount_type' => $request->discount_type,
                    'discount_value' => $request->discount_value,

                    'add_tax' => $mainBill->add_tax,
                    'tax_name' => $mainBill->tax_name,
                    'tax_value' => $mainBill->tax_value,

                    'send_sms' => $mainBill->send_sms,
                    'send_email' => $mainBill->send_email,

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

                if ($mainBill->add_tax) {
                    $vat = ($sub_total + $payment_fees - $discount) * $mainBill->tax_value / 100;
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
                $bill->debit_note_bill_id = $mainBill->id;
                $bill->save();
                return $bill;
            });

            event(new BillCreated($bill));
            return redirect()->route('bills.show', $mainBill);
        }else{
            abort(403);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Bill $bill)
    {
        $this->authorize('checkPermission', $bill);
        $title = $bill->debit_note_bill_id == null ? __('Bill No.') : __('Debit Note No.');

        $debitNotes = Bill::where('debit_note_bill_id', $bill->id)
            ->select('id', DB::raw("CONCAT('DN', number) AS number"), 'sub_total', 'vat', 'discount', 'created_at', DB::raw("'bills' as model"));

        $creditNotes = RefundedBill::where('bill_id', $bill->id)
        ->select('id', DB::raw("CONCAT('CN', number) as number"), 'amount as sub_total', DB::raw("'0' as vat"), DB::raw("'0' as discount"), 'created_at', DB::raw("'refundedbills' as model"));

        $billNotes = $debitNotes->union($creditNotes)->orderBy('created_at', 'desc')->get();

        return view('bills.show', ['bill' => $bill, 'title' => $title, 'billNotes' => $billNotes]);
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
            session()->put('user-lang', $lang);
        } else {
            \App::setLocale($bill->user->settings->default_lang);
        }

        if (!$bill) {
            abort(404);
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

        $years = [];
        $microformSessionToken = $billSignature = $payTime = null;
        if(config('payment.default_payment_gateway') == 'cybersource')
        {
            $years = range(date('Y'), date('Y') + 10);
            // $microformSessionToken = CybersourceMicroformHandlerHelper::retrieveMicroformToken();
            $payTime = now()->unix();
            $billSignature = BillSignatureHelper::generateSignature($bill, $payTime);
            $payForm = 'bills.cybersource_pay_form';
        }
        else
        {
            $payForm = 'bills.mastercard_pay_form';
        }
        if ($bill->application_id == null || !$bill->user->settings->api_bill_style) {
            return view('bills.pay', compact('bill', 'id', 'countdown', 'payForm', 'years', 'microformSessionToken', 'billSignature', 'payTime'));
        }

        return view('bills.payment_page', compact('bill', 'id', 'countdown', 'payForm', 'years', 'microformSessionToken', 'billSignature', 'payTime'));
    }

    public function paymentWaiting(Request $request){
        $jwt = $request->jwt;
        if($request->challange){
            $iframe = 'bills.cybersource.challange_iframe';
        }else{
            $iframe = 'bills.cybersource.data_collection_iframe';
        }
        return view('bills.payment_waiting', compact( 'iframe', 'jwt'));
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
        $this->authorize('checkPermission', $bill);

        if($bill->status != 'pending'){
            return redirect()->back()->with('error', __('You cannot cancel this bill'));
        }

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
        $this->authorize('checkPermission', $bill);

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
        $this->authorize('checkPermission', $bill);

        \Log::channel('refunded_transactions')->info("refunded transaction from BillController at refund method ", array($bill->id, $request->amount));

        if(!$bill->is_able_refund){
            return redirect()->back()->withErrors(['refund' => __("You can't refund this bill now please try again later")]);
        }

        if($bill->debit_note_bill_id != null){
            return redirect()->back()->withErrors(['refund' => __("You can't refund this Debit Note")]);
        }

        $method = $bill->getRefundedMethod();

        if ($request->type == 'partial_refund') {
            if($bill->status == 'paid' && $request->amount > $bill->user->balance){
                return redirect()->back()->withErrors(['refund' => __("Quantity must be less than or equal to the user's balance")]);
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
            if ($bill->is_able_total_refund) {
                $totalRefundAmountWithFees = $bill->due_to_client;
                $totalRefundAmountWithoutFees = $bill->total;

                if ($bill->setRefunded()) {

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

                    return redirect()->back();
                }
            }else{
                return redirect()->back()->withErrors(['refund' => __("Quantity must be less than or equal to the user's balance")]);
            }
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
        $this->authorize('checkPermission', $bill);

        return view('bills.invoice', compact('bill', 'id'));
    }

    public function billPrint($id, Request $request)
    {
        $bill = Bill::find($id);
        $this->authorize('checkPermission', $bill);

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

    public function export(Request $request){

        $filter['user_id'] = auth()->user()->store_main_user_id ?? auth()->user()->id;
        $filter['date_start'] = $request->date_start ?? null;
        $filter['date_to'] = $request->date_to ?? null;

        if(monthsCounter($filter['date_start'], $filter['date_to']) > config('exportationLimit.merchant_bills_exportation')){
            return redirect()->back()->withErrors(['alert' => __("Your exportation request period must be equal or less than :number months", ['number' => config('exportationLimit.merchant_bills_exportation')])]);
        }

        if (!$request->dont_update_statuses) {
            session(['status_filters' => $request->statuses]);
        }

        $filter['statuses'] = array();
        if($request->statuses){
            $filter['statuses'] = $request->statuses;
            $filter['statuses'] = in_array('paid', $filter['statuses']) ? array_merge($filter['statuses'], ['paid_cash', 'paid_bank_transfer', 'paid_machine', 'refunded_cash', 'refunded_bank_transfer', 'refunded', 'refunded_machine']) : $filter['statuses'];
        }

        // dispatch job
        ExportMerchantBills::dispatch($filter, [auth()->user()->email]);

        //redirect to index with alert
        return redirect()->back()->with(['success' => __("You export request will be send to your mail just be ready")]);
    }
}
