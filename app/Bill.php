<?php

namespace App;

use App\Events\BillPaid;
use App\Events\BillStatusUpdated;
use App\PaymentLog;
use App\Traits\UsesUuid;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class Bill extends Model
{    
    use UsesUuid;

    protected $fillable = [
    	'status', 
    	'payment_method', 
    	'user_id', 
    	'customer_id', 
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
        
        'expiry_minutes',
        'expiry_hours',
        'pricing_fees_details',
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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $appends = [
        'trans_status',
    ];
    
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
        return ($this->status == 'pending') ;
    }

    /**
     * Pay Url.
     *
     * @var string
     */
    public function getPayUrlAttribute()
    {
        return route('paybillpage', ['id' => $this->pay_id]);
    }   

    /**
     * Back Url.
     *
     * @var string
     */
    public function getBackUrlAttribute()
    {
        return $this->application->redirect.'?reference_id='.$this->reference_id.'&status=fail&bill_id='.$this->id.'&pay_url='.$this->pay_url;
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
        $date = $this->created_at
                ->addDays($this->expiry_date)
                ->addMinutes($this->expiry_minutes)
                ->addHours($this->expiry_hours);
        return $date->isPast();
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
        if(!$this->is_expired){
            $totalDuration = Carbon::now()->diffInSeconds($date);
            return gmdate('i', $totalDuration);
        }else{
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
        if(!$this->is_expired){
            $totalDuration = Carbon::now()->diffInSeconds($date);
            return gmdate('H', $totalDuration);
        }else{
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
        if(!$this->is_expired){
            $totalDuration = Carbon::now()->diffInSeconds($date);
            return gmdate('s', $totalDuration);
        }else{
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

        return self::find($id??null);
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
        return $this->where('id', $value)->where('user_id', auth()->user()->id)->firstOrFail();
    }

    /**
     * get only paid bills
     */
    public function scopePaid($query){
        $query->where('status', 'paid');
    }

    /**
     * get only pending bills
     */
    public function scopePending($query){
        $query->where('status', 'pending');
    }

    /**
     * get only paid bills
     */
    public function scopeNotSettled($query){
        $query->where('settled', false);
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
    public function depositTransaction()
    {
        return $this->hasOne(Transaction::class)->where('type', 'credit');
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
    public function paid()
    {
        if ($this->status == 'paid') {
            return false;
        }

        $this->status = 'paid';
        $this->paid_at = Carbon::now();
        $this->payment_method = 'credit';
        $this->save();

        event(new BillPaid($this));
        event( new BillStatusUpdated($this) );
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
     * Get customer.
     *
     * @return Collection
     */
    public function getNumber()
    {
        $number = self::max('number');

        return $number == 0 ? 1000001 : $number + 1;
    } 
}
