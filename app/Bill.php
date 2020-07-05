<?php

namespace App;

use App\Events\BillPaid;
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
        'client_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'due_date' => 'datetime:Y-m-d',
    ];
    
    public function getPayIdAttribute()
    {
        $uuid = Uuid::fromString($this->id);
        $hex = $uuid->getHex();
        $hashids = new Hashids();
        return $hashids->encodeHex($hex);
    }

    public function getPayUrlAttribute()
    {
        return route('paybillpage', ['id' => $this->pay_id]);
    }    

    public function getIsInvalidAttribute()
    {
        return ($this->status != 'pending');
    }

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
     * Get items.
     *
     * @return Collection
     */
    public function items()
    {
        return $this->hasMany(BillItem::class);
    }  

    /**
     * Get client.
     *
     * @return Collection
     */
    public function client()
    {
        return $this->belongsTo(OauthClient::class, 'client_id');
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
        return 1000001 + self::max('number');
    } 
}
