<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * CouponCode Model
 * 
 * Represents individual codes for ONE_TIME_USAGE mechanism.
 * Each code can be used only once by a single customer.
 */
class CouponCode extends Model
{
    protected $fillable = [
        'coupon_id',
        'code',
        'is_used',
    ];

    protected $casts = [
        'is_used' => 'boolean',
    ];

    /**
     * Get the coupon this code belongs to
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get usage record for this code
     */
    public function usage(): HasMany
    {
        return $this->hasMany(CouponUsage::class, 'coupon_code_id');
    }

    /**
     * Check if code is available for use
     */
    public function getIsAvailableAttribute(): bool
    {
        if ($this->is_used) {
            return false;
        }

        if (!$this->coupon->is_valid) {
            return false;
        }

        return true;
    }
}
