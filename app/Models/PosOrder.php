<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Ramsey\Uuid\Uuid;
use App\Traits\UsesUuid;

class PosOrder extends Model
{
    use UsesUuid;

    protected $fillable = [
        'user_id',
        'customer_id',
        'business_name',
        'customer_name',
        'customer_mobile',
        'customer_email',
        'customer_notes',
        'bullding_no',
        'street_name',
        'district',
        'city',
        'postal_code',
        'additional_no',
        'other_buyer_id',
        'vat_registration_number',
        'add_discount',
        'discount_type',
        'discount_value',
        'add_tax',
        'tax_name',
        'tax_value',
        'sub_total',
        'vat',
        'discount',
        'total',
        'payment_method'
    ];

    /**
     * Get items.
     *
     * @return Collection
     */
    public function items()
    {
        return $this->hasMany(PosOrderItem::class, 'order_id');
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
