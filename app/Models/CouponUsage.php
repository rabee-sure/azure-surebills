<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CouponUsage Model
 * 
 * Tracks each usage of a coupon.
 * Records which customer used which coupon/code and when.
 */
class CouponUsage extends Model
{
    protected $fillable = [
        'coupon_id',
        'coupon_code_id',
        'customer_id',
        'bill_id',
        'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    /**
     * Get the coupon that was used
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the specific code that was used (for ONE_TIME_USAGE)
     */
    public function couponCode(): BelongsTo
    {
        return $this->belongsTo(CouponCode::class);
    }

    /**
     * Get the customer who used the coupon
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the bill where the coupon was used
     */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Scope: Filter by coupon
     */
    public function scopeForCoupon($query, int $couponId)
    {
        return $query->where('coupon_id', $couponId);
    }

    /**
     * Scope: Filter by customer
     */
    public function scopeForCustomer($query, int $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
}
