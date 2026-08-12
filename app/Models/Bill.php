<?php

namespace App\Models;

use Carbon\Carbon;
use Hashids\Hashids;
use Ramsey\Uuid\Uuid;
use App\Events\BillPaid;
use App\Traits\UsesUuid;
use App\Models\PaymentLog;
use App\Models\WebhookLog;
use App\Events\BillRefunded;
use App\Events\BillOfflineRefunded;
use App\Events\BillStatusUpdated;
use App\Events\BillPartialRefunded;
use App\Events\BillOfflinePartialRefunded;
use Illuminate\Database\Eloquent\Model;
use App\Events\BillTransactionConfirmed;
use Laravel\Sanctum\HasApiTokens;

class Bill extends Model
{
    use UsesUuid, HasApiTokens;

    protected $fillable = [
        'status',
        'payment_method',
        'user_id',
        'created_by',
        'customer_id',
        'coupon_id',
        'business_name',
        'customer_name',
        'customer_mobile',
        'customer_email',
        'customer_notes',
        'reference_id',
        'due_date',
        'expiry_date',
        'add_discount',
        'discount_type',
        'discount_value',
        'add_tax',
        'tax_name',
        'tax_value',
        'send_sms',
        'send_email',
        'sub_total',
        'vat',
        'discount',
        'total',
        'paid_at',
        'canceled_at',
        'application_id',
        'payment_fees',
        'settled',
        'channel_settled',

        'expiry_minutes',
        'expiry_hours',
        'pricing',
        'pricing_fees_details',
        'is_redirect',

        'payment_channel_fees',
        'payment_channel_fees_vat',

        'payment_surebills_fees',
        'payment_surebills_fees_vat',
        'refunded_at',
        'refund_amount',

        'pending_settled',

        'bill_redirect_url',
        'bill_webhook_url',

        'channel_extra_amount',
        'channel_extra_title',
        'channel_extra_vat',
        'source',
        'payment_way'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'due_date' => 'datetime:Y-m-d',
        'canceled_at' => 'datetime',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'pricing' => 'array',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $appends = [
        'trans_status',
    ];

    public function scopeUserId($query, $value)
    {
        return $query->where('user_id', $value);
    }

    public function scopeCreatedBy($query, $value)
    {
        return $query->where('created_by', $value);
    }

    public function scopeSource($query, $value)
    {
        return $query->where('source', $value);
    }

    /**
     * Pay Id.
     *
     * @var array
     */
    public function getHyperpayIdAttribute()
    {
        if (isset($this->payment_logs[0]) && isset($this->payment_logs[0]->results) && isset($this->payment_logs[0]->results['response']) && isset($this->payment_logs[0]->results['response']['id'])) {
            return $this->payment_logs[0]->results['response']['id'];
        }

        return null;
    }

    /**
     * Pay Id.
     *
     * @var array
     */
    public function isHaveChannelOwenByUser($user_id)
    {
        if ($this->application_id && isset($this->application)) {
            if ($this->application->channel_id && isset($this->application->channel) && $this->application->channel->user_id == $user_id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Pay Id.
     *
     * @var array
     */
    public function getPayIdAttribute()
    {
        $uuid = Uuid::fromString($this->id);
        $hex = $uuid->getHex();
        $hashids = new Hashids();
        return $hashids->encodeHex($hex);
    }

    /**
     * Pay Id.
     *
     * @var array
     */
    public function getBillTitleAttribute()
    {
        return __('Bill') . ' ' . $this->number . ' - ' . $this->customer_name;
    }

    /**
     * Redirect Url.
     *
     * @var string
     */
    public function getIsAbleRefundAttribute()
    {
        $ableToRefund = false;
        if(in_array($this->status, ['paid', 'paid_cash', 'paid_bank_transfer', 'paid_machine'])){
            $ableToRefund = $this->user->able_refund
            && $this->total > 0
            && ($this->paid_at && $this->paid_at->gt(Carbon::parse('2021-02-04 03:05:33')))
            && $this->has_delayed_refund_transaction
            && !$this->has_pending_refund;
            if($this->status == 'paid'){
                $ableToRefund = $ableToRefund && $this->bill_paid;
            }

        }
        
        return $ableToRefund;
    }

    public function getHasDelayedRefundTransactionAttribute(){
        $last_refund_transaction = Transaction::where('bill_id', $this->id)->where('transaction_source', 'refund')->orderBy('created_at', 'desc')->first();
        if($last_refund_transaction){
            return $last_refund_transaction->created_at < Carbon::now()->subMinutes(10)->toDateTimeString() ? true : false;
        }else{
            return true;
        }
    }

    /**
     * Redirect Url.
     *
     * @var string
     */
    public function getHasPendingRefundAttribute()
    {
        $pending_refund = PaymentLog::where('payment_logs.payment_method', 'mastercard_refund')
            ->where('payment_logs.webhook_response_received', false)
            ->where('payment_logs.is_failure', false)
            ->where('bills.user_id', $this->user_id)
            ->join('bills', 'bills.id', '=', 'payment_logs.bill_id')
            ->count();

        return $pending_refund > 0 ? true : false;
    }

    public function getBillPaidAttribute()
    {
        $bill_paied = PaymentLog::whereIn('payment_method', ['mastercard_pay','mastercard_applepay'])
            ->where('webhook_response_received', true)
            ->where('bill_id', $this->id)
            ->count();

        return $bill_paied > 0 ? true : false;
    }

    /**
     * Redirect Url.
     *
     * @var string
     */
    public function getIsAbleChangeStatusAttribute()
    {
        return $this->status == 'pending';
    }


    /**
     * Redirect Url.
     *
     * @var string
     */
    public function getIsAbleTotalRefundAttribute()
    {
        if ($this->status == 'paid') {
            $with_fees = $this->is_able_refund && round($this->due_to_client) <= round($this->user->actual_balance);
            $without_fees = $this->is_able_refund && $this->total <= $this->user->actual_balance;

            return $this->user->able_refund_with_fees ? $with_fees : $without_fees;
        } else {
            return true;
        }
    }


    /**
     * Translation Status.
     *
     * @var array
     */
    public function getTransStatusAttribute()
    {
        return __(ucfirst($this->status));
    }

    /**
     * Is Pending.
     *
     * @var boolean
     */
    public function getIsPendingAttribute()
    {
        return ($this->status == 'pending');
    }

    /**
     * Pay Url.
     *
     * @var string
     */
    public function getPayUrlAttribute()
    {
        return route('paybillpagelang', ['id' => $this->pay_id, 'lang' => $this->user->settings->default_lang]);
    }
    
    // get access to pay page attribute
    public function getAccessToPayPageAttribute()
    {
        $accessToPayPage = ['status' => true, 'message' => ''];
        // Prevent access if bill already paid
        if ($this->status != 'pending') {
            $accessToPayPage = ['status' => false, 'message' => 'This bill not pending you can not access it'];
            return (object)$accessToPayPage;
        }

        // Prevent access if bill is older than pay page expiration time
        if(config('bills.pay_page_expiration_time_type') == 'Days')
        {
            if ($this->created_at->lt(now()->subDays(config('bills.pay_page_expiration_time')))) {
                $accessToPayPage = ['status' => false, 'message' => 'This payment link has expired.'];
            }
        }
        else if(config('bills.pay_page_expiration_time_type') == 'Hours')
        {
            if ($this->created_at->lt(now()->subHours(config('bills.pay_page_expiration_time')))) {
                $accessToPayPage = ['status' => false, 'message' => 'This payment link has expired.'];
            }
        }
        else if(config('bills.pay_page_expiration_time_type') == 'Minutes')
        {
            if ($this->created_at->lt(now()->subMinutes(config('bills.pay_page_expiration_time')))) {
                $accessToPayPage = ['status' => false, 'message' => 'This payment link has expired.'];
            }
        };

        return (object)$accessToPayPage;
    }

    /**
     * Pay Url.
     *
     * @var string
     */
    public function getInvoiceUrlAttribute()
    {
        return route('invoice', ['id' => $this->pay_id, 'lang' => app()->getLocale()]);
    }
    /**
     * Back Url.
     *
     * @var string
     */
    public function getBackUrlAttribute()
    {
        $data = [
            'bill_number=' . $this->number,
            'reference_id=' . $this->reference_id,
            'status=fail',
            'bill_id=' . $this->id,
            'pay_url=' . $this->pay_url,
        ];
        $ks = (str_contains($this->application->redirect, '?')) ? "&" : '?';
        return $this->application->redirect . $ks . implode("&", $data);
    }

    /**
     * Redirect Url.
     *
     * @var string
     */
    public function getRedirectUrlAttribute()
    {
        $link = $this->bill_redirect_url ?? $this->application->redirect;
        $data = [
            'bill_number=' . $this->number,
            'reference_id=' . $this->reference_id,
            'status=' . $this->status,
            'bill_id=' . $this->id,
            'pay_url=' . $this->pay_url,
            'total=' . $this->total,
        ];

        $ks = (str_contains($link, '?')) ? "&" : '?';
        return $link . $ks . implode("&", $data);
    }

    /**
     * Redirect Url.
     *
     * @var string
     */
    public function getRedirectUrl($log_resault=null)
    {
        $link = $this->bill_redirect_url ?? $this->application->redirect;
        $data = [
            'bill_number='.$this->number,
            'reference_id='.$this->reference_id,
            'status='.$this->status,
            'bill_id='.$this->id,
            'pay_url='.$this->pay_url,
            'total='.$this->total,
        ];
        if($log_resault){
            $data[] = 'payment_brand='.$log_resault['paymentBrand']??null;
            $data[] = 'last_4_digits='.$log_resault['card']['last4Digits']??null;
            $data[] = 'code='.$log_resault['result']['code']??null;
            $data[] = 'description='.$log_resault['result']['description']??null;
        }

        $ks = (str_contains($link, '?')) ? "&" : '?';
        return $link . $ks . implode("&", $data);
    }



    /**
     * webhook Url.
     *
     * @var string
     */
    public function getWebhookUrlAttribute()
    {
        if (!$this->application) {
            return null;
        }

        $data = [
            'bill_number=' . $this->number,
            'reference_id=' . $this->reference_id,
            'status=' . $this->status,
            'bill_id=' . $this->id,
            'pay_url=' . $this->pay_url,
            'total=' . $this->total,
        ];

        $ks = (str_contains($this->application->webhook_url, '?')) ? "&" : '?';
        $link = $this->bill_webhook_url ?? $this->application->webhook_url;
        return $this->link . $ks . implode("&", $data);
    }
    /**
     * Success Payment.
     *
     * @var string
     */
    public function getLastPaymentAttribute()
    {
        return PaymentLog::where('bill_id', $this->id)->orderBy('id', 'desc')->first();
    }

    /**
     * Success Payment.
     *
     * @var string
     */
    public function getSuccessPaymentAttribute()
    {
        return PaymentLog::where('bill_id', $this->id)->where('status', 1)->orderBy('id', 'desc')->first();
    }

    /**
     * Is Expired.
     *
     * @var boolean
     */
    public function getIsExpiredAttribute()
    {
        if ($this->expiry_date == 0 && $this->expiry_hours == 0 && $this->expiry_minutes == 0) {
            return false;
        }

        $date = $this->created_at
            ->addDays($this->expiry_date)
            ->addMinutes($this->expiry_minutes)
            ->addHours($this->expiry_hours);
        return $date->isPast();
    }

    /**
     * Payment Method Details.
     *
     * @var boolean
     */
    public function getPaymentMethodDetailsAttribute()
    {
        $method = '';

        if (!$this->success_payment) {
            return $method;
        }

        if ($this->success_payment->payment_method == 'hyperpay_applepay') {
            $method .= 'APPLE PAY - ';
        }

        if (isset($this->success_payment->results['response']) && isset($this->success_payment->results['response']['paymentBrand'])) {
            $method .= $this->success_payment->results['response']['paymentBrand'];
        }

        if ($this->success_payment->brand) {
            $method .= $this->success_payment->brand;
        }

        return $method;
    }

    /**
     * Payment Method Details.
     *
     * @var boolean
     */
    public function getDueToClientAttribute()
    {
        return $this->total - $this->payment_fees - $this->payment_fees_vat;
    }

    /**
     * Is Expired.
     *
     * @var boolean
     */
    public function getRemainingTimeMinutesAttribute()
    {
        $date = $this->created_at
            ->addDays($this->expiry_date)
            ->addMinutes($this->expiry_minutes)
            ->addHours($this->expiry_hours);
        if (!$this->is_expired) {
            $totalDuration = Carbon::now()->diffInSeconds($date);
            return gmdate('i', $totalDuration);
        } else {
            return "00";
        }
    }

    /**
     * Is Expired.
     *
     * @var boolean
     */
    public function getRemainingTimeHoursAttribute()
    {
        $date = $this->created_at
            ->addDays($this->expiry_date)
            ->addMinutes($this->expiry_minutes)
            ->addHours($this->expiry_hours);
        if (!$this->is_expired) {
            $totalDuration = Carbon::now()->diffInSeconds($date);
            return [
                'days' => Carbon::now()->diffInDays($date),
                'hours' => gmdate('H', $totalDuration),
            ];
        } else {
            return "00";
        }
    }
    /**
     * Is Expired.
     *
     * @var boolean
     */
    public function getRemainingTimesecondsAttribute()
    {
        $date = $this->created_at
            ->addDays($this->expiry_date)
            ->addMinutes($this->expiry_minutes)
            ->addHours($this->expiry_hours);
        if (!$this->is_expired) {
            $totalDuration = Carbon::now()->diffInSeconds($date);
            return gmdate('s', $totalDuration);
        } else {
            return "00";
        }
    }

    /**
     * Is Invalid.
     *
     * @var boolean
     */
    public function getIsInvalidAttribute()
    {
        return ($this->status != 'pending');
    }

    /**
     * The attributes that should be cast.
     *
     * @var string
     */
    static public function decodeId($hashed_id)
    {
        $hashids = new Hashids();
        $hex = $hashids->decodeHex($hashed_id);
        $id = array_reduce([20, 16, 12, 8], function ($uuid, $offset) {
            return substr_replace($uuid, '-', $offset, 0);
        }, str_pad($hex, 32, '0', STR_PAD_LEFT));

        return self::find($id ?? null);
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)->userId(auth()->user()->store_main_user_id ?? auth()->user()->id)->firstOrFail();
    }

    /**
     * get only paid bills
     */
    public function scopePaid($query)
    {
        $query->where('status', 'paid');
    }

    /**
     * get only paid bills
     */
    public function scopeRefunded($query)
    {
        $query->where('status', 'refunded');
    }

    /**
     * get only paid bills
     */
    public function scopeSettled($query)
    {
        $query->where('settled', true);
    }

    /**
     * get only pending bills
     */
    public function scopePending($query)
    {
        $query->where('status', 'pending');
    }

    /**
     * get only paid bills
     */
    public function scopeNotSettled($query)
    {
        $query->where('settled', false);
    }

    /**
     * get only paid bills that doesn't have succeded webhook call
     */
    public function scopePaidButNotHaveSuccessWebhook($query)
    {
        $query
            ->whereHas('application')->paid()
            ->where('paid_at', '>', '2021-06-09')
            ->where(function ($query) {
                $query->where('bills.is_callbacked', '!=', true)
                    ->orWhereDoesntHave('webhookLogs', function ($query) {
                        $query->where('webhook_logs.status_code', 200);
                    });
            });
    }

    /**
     * Get items.
     *
     * @return Collection
     */
    public function items()
    {
        return $this->hasMany(BillItem::class);
    }

    /**
     * Get items.
     *
     * @return Collection
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get items.
     *
     * @return Collection
     */
    public function depositTransaction()
    {
        return $this->hasOne(Transaction::class)->where('type', 'credit');
    }

    /**
     * Get items.
     *
     * @return Collection
     */
    public function withdrawTransactions()
    {
        return $this->hasMany(Transaction::class)->where('type', 'debit');
    }

    /**
     * Get application.
     *
     * @return Collection
     */
    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get application.
     *
     * @return Collection
     */
    public function channel()
    {
        return $this->application->channel;
    }

    /**
     * Get user.
     *
     * @return Collection
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark invoice as paid
     */
    public function setPaid()
    {
        if ($this->status == 'paid') {
            return false;
        }

        $this->status = 'paid';
        $this->paid_at = Carbon::now();
        $this->payment_method = 'credit';
        $this->save();
    }

    /**
     * Fire paid event
     */
    public function firePaidEvent($payment)
    {
        if ($this->paid_event_fired) {
            return false;
        }

        $this->paid_event_fired = true;
        $this->save();

        event(new BillPaid($this, $payment));
        event(new BillStatusUpdated($this, $payment));
    }

    /**
     * Fire refund event
     */
    public function fireRefundEvent($payment, $partial_amount = false)
    {
        if ($this->refund_event_fired) {
            return false;
        }

        $this->refund_event_fired = true;
        $this->save();

        if ($partial_amount) {
            event(new BillPartialRefunded($this, $payment, $partial_amount));
        } else {
            event(new BillRefunded($this, $payment));
        }
        event(new BillStatusUpdated($this, $payment));
    }

    /**
     * Mark invoice as refunded
     */
    public function setRefunded()
    {
        if (!$this->is_able_refund) {
            if ($this->has_pending_refund) {
                session(['refund_error' => __('There is already a pending refund on your account.')]);
            } else {
                session(['refund_error' => __('You can not refund this bill.')]);
            }
            return false;
        }

        if ($this->success_payment && $this->success_payment->refund($this->total)) {
            $this->refund_event_fired = false;
            $this->status = 'refunded';
            $this->refunded_at = Carbon::now();
            $this->refund_amount = $this->refund_amount + $this->total;
            $this->save();

            return true;
        } else if (!$this->success_payment) {
            $total_remain = $this->total;
            if($this->status == 'paid_cash'){
                $this->status = 'refunded_cash';
            }elseif($this->status == 'paid_bank_transfer'){
                $this->status = 'refunded_bank_transfer';
            }elseif($this->status == 'paid_machine'){
                $this->status = 'refunded_machine';
            }
            $this->refund_amount = $this->refund_amount + $this->total;
            $this->total = 0;
            $this->refunded_at = Carbon::now();
            $this->save();

            event(new BillOfflineRefunded($this, $total_remain));

            return true;
        }

        return false;
    }


    /**
     * Mark invoice as Partial refunded
     */
    public function setPartialRefunded($amount)
    {
        if (!$this->is_able_refund) {
            return false;
        }

        if ($this->success_payment && $this->success_payment->refund($amount)) {

            $this->refund_event_fired = false;
            $this->refund_amount = $this->refund_amount + $amount;
            $this->save();

            return true;
        } else if (!$this->success_payment) {
            $this->total = $this->total - $amount;
            $this->refund_amount = $this->refund_amount + $amount;
            $this->save();

            event(new BillOfflinePartialRefunded($this, $amount));

            return true;
        }

        return false;
    }

    /**
     * Get customer.
     *
     * @return Collection
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the coupon used for this bill
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get customer.
     *
     * @return Collection
     */
    public function getNumber()
    {
        $number = self::max('number');
        $refundedBillNumber = RefundedBill::max('number');

        if($number > $refundedBillNumber){
            return $number == 0 ? 1000001 : $number + 1;
        }else{
            return $refundedBillNumber == 0 ? 1000001 : $refundedBillNumber + 1;
        }
    }


    /**
     * ReCalculate payment fess
     */
    public function reCalculateFees()
    {
        // update bill
        $percentage = $this->getPercentage();
        $fixed = $this->getFixed();
        $this->pricing_fees_details = $percentage . '%,' . $fixed;
        $this->payment_fees = $this->total * ($percentage / 100) + $fixed;
        $this->payment_fees_vat = $this->payment_fees * (Transaction::VAT_PERCENTAGE / 100);
        $this->save();

        // update transactions
        foreach ($this->withdrawTransactions as $transaction) {
            if ($transaction->description == 'Fee - Transaction Processing') {
                if ($transaction->amount < $this->payment_fees) {
                    $transaction->balance = $transaction->balance - ($this->payment_fees - $transaction->amount);
                } else {
                    $transaction->balance = $transaction->balance + ($transaction->amount - $this->payment_fees);
                }
                $transaction->amount  = $this->payment_fees;
                $transaction->save();
            } else if ($transaction->description == 'VAT - Transaction Processing') {
                if ($transaction->amount < $this->payment_fees_vat) {
                    $transaction->balance = $transaction->balance - ($this->payment_fees_vat - $transaction->amount);
                } else {
                    $transaction->balance = $transaction->balance + ($transaction->amount - $this->payment_fees_vat);
                }
                $transaction->amount = $this->payment_fees_vat;
                $transaction->save();
            }
        }
    }

    /**
     * Get payment_logs.
     *
     * @return Collection
     */
    public function payment_logs()
    {
        return $this->hasMany(PaymentLog::class)->orderBy('id', 'desc')
            ->where('payment_logs.payment_method', '!=', 'mastercard_auth');
    }

    public function dateLocalization()
    {
        return Carbon::parse($this->due_date->format('Y-m-d'))
            ->locale(app()->getLocale())
            ->translatedFormat('j F Y');
    }

    /**
     * get Percentage from object.
     *
     * @return double
     */
    public function getPercentage($log, $from_channel = false)
    {
        if (isset($this->application) && isset($this->application->channel)) {
            $object = $from_channel ? $this->application->channel : $this->application;
        } else {
            $object = $this->user;
        }

        return $log->brand == 'MADA' ? $object->mada_percentage : $object->credit_cards_percentage;
    }

    /**
     * get Fixed from object.
     *
     * @return double
     */
    public function getFixed($log, $from_channel = false)
    {
        if (isset($this->application) && isset($this->application->channel)) {
            $object = $from_channel ? $this->application->channel : $this->application;
        } else {
            $object = $this->user;
        }

        return $log->brand == 'MADA' ? $object->mada_fixed : $object->credit_cards_fixed;
    }


    // this is a recommended way to declare event handlers
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($bill) {
            // before delete() method call this
            $bill->items()->delete();
            $bill->transactions()->delete();
            // do the rest of the cleanup...
        });
    }

    /**
     * Get all of the webhookLogs for the Bill
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function webhookLogs()
    {
        return $this->hasMany(WebhookLog::class, 'bill_id', 'id');
    }

    public function refundedBills(){
        return $this->hasMany(RefundedBill::class);
    }

    public function getRefundedMethod(){
        $method = '';
        switch ($this->status) {
            case 'paid':
                $method = 'online';
                break;
            case 'paid_cash':
                $method = 'cash';
                break;
            case 'paid_bank_transfer':
                $method = 'bank_transfer';
                break;
            case 'paid_machine':
                $method = 'payment_machine';
                break;
            case 'refunded':
                $method = 'online';
                break;
            case 'refunded_cash':
                $method = 'cash';
                break;
            case 'refunded_bank_transfer':
                $method = 'bank_transfer';
                break;
            case 'refunded_machine':
                $method = 'payment_machine';
                break;

            default:
                # code...
                break;
        }

        return $method;
    }

    public function mainBill()
    {
        return $this->belongsTo(Bill::class, 'debit_note_bill_id', 'id');
    }

    public function billDebitNotes()
    {
        return $this->hasMany(Bill::class, 'debit_note_bill_id', 'id');
    }

}
