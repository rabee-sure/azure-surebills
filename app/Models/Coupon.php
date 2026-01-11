<?php

namespace App\Models;

use App\Enums\CouponMechanism;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Coupon Model
 * 
 * Represents a coupon template/definition.
 * Each coupon can have multiple codes (for ONE_TIME_USAGE mechanism)
 * or be a single reusable code (for MAX_USAGE or MAX_CUSTOMER_USAGE).
 */
class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'mechanism',
        'discount_type',
        'discount_value',
        'valid_from',
        'valid_to',
        'max_usage',
        'max_customer_usage',
        'code_pattern',
        'is_active',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
        'max_usage' => 'integer',
        'max_customer_usage' => 'integer',
    ];

    /**
     * Get mechanism as CouponMechanism instance
     */
    public function getMechanismAttribute($value)
    {
        if ($value instanceof CouponMechanism) {
            return $value;
        }
        if (is_string($value)) {
            return CouponMechanism::tryFrom($value);
        }
        return null;
    }

    /**
     * Set mechanism from CouponMechanism instance or string
     */
    public function setMechanismAttribute($value)
    {
        if ($value instanceof CouponMechanism) {
            $this->attributes['mechanism'] = $value->value();
        } else {
            $this->attributes['mechanism'] = $value;
        }
    }

    /**
     * Get the merchant (user) that owns this coupon
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all generated codes for this coupon (ONE_TIME_USAGE mechanism)
     */
    public function codes(): HasMany
    {
        return $this->hasMany(CouponCode::class);
    }

    /**
     * Get all usages of this coupon
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Get total usage count
     */
    public function getTotalUsageAttribute(): int
    {
        return $this->usages()->count();
    }

    /**
     * Get remaining usage count
     */
    public function getRemainingUsageAttribute(): ?int
    {
        if ($this->mechanism && $this->mechanism->value() === CouponMechanism::MAX_USAGE && $this->max_usage) {
            return max(0, $this->max_usage - $this->total_usage);
        }
        
        if ($this->mechanism && $this->mechanism->value() === CouponMechanism::ONE_TIME_USAGE) {
            $totalCodes = $this->codes()->count();
            $usedCodes = $this->codes()->where('is_used', true)->count();
            return max(0, $totalCodes - $usedCodes);
        }
        
        return null;
    }

    /**
     * Check if coupon is currently valid
     */
    public function getIsValidAttribute(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        
        if ($this->valid_from && $now->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_to && $now->gt($this->valid_to)) {
            return false;
        }

        if ($this->mechanism && $this->mechanism->value() === CouponMechanism::MAX_USAGE && $this->max_usage) {
            return $this->total_usage < $this->max_usage;
        }

        return true;
    }

    /**
     * Check if coupon is expired
     */
    public function getIsExpiredAttribute(): bool
    {
        if ($this->valid_to) {
            return now()->gt($this->valid_to);
        }
        return false;
    }

    /**
     * Scope: Filter by merchant
     */
    public function scopeForMerchant($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Only active coupons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Only valid (not expired, within date range, not exhausted)
     */
    public function scopeValid($query)
    {
        $now = now();
        
        return $query->where('is_active', true)
            ->where(function($q) use ($now) {
                $q->whereNull('valid_from')
                  ->orWhere('valid_from', '<=', $now);
            })
            ->where(function($q) use ($now) {
                $q->whereNull('valid_to')
                  ->orWhere('valid_to', '>=', $now);
            });
    }
}
