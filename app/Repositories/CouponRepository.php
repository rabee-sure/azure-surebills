<?php

namespace App\Repositories;

use App\Models\Coupon;
use App\Models\CouponCode;
use App\Models\CouponUsage;
use App\Enums\CouponMechanism;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * CouponRepository
 * 
 * Data access layer for coupon operations.
 * Handles all database queries related to coupons.
 */
class CouponRepository
{
    /**
     * Get paginated coupons for a merchant
     */
    public function getForMerchant(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Coupon::forMerchant($userId)
            ->withCount(['codes', 'usages'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Find coupon by ID for a specific merchant
     */
    public function findForMerchant(int $couponId, int $userId): ?Coupon
    {
        return Coupon::forMerchant($userId)
            ->with(['codes', 'usages.customer'])
            ->find($couponId);
    }

    /**
     * Create a new coupon
     */
    public function create(array $data): Coupon
    {
        return Coupon::create($data);
    }

    /**
     * Update an existing coupon
     */
    public function update(Coupon $coupon, array $data): bool
    {
        return $coupon->update($data);
    }

    /**
     * Delete a coupon
     */
    public function delete(Coupon $coupon): bool
    {
        return $coupon->delete();
    }

    /**
     * Find coupon by code (for reusable codes)
     */
    public function findByCode(string $code, int $userId): ?Coupon
    {
        return Coupon::forMerchant($userId)
            ->where('code_pattern', $code)
            ->where('mechanism', '!=', CouponMechanism::ONE_TIME_USAGE)
            ->first();
    }

    /**
     * Find coupon code by code string (for ONE_TIME_USAGE)
     */
    public function findCodeByCode(string $code, int $userId): ?CouponCode
    {
        return CouponCode::whereHas('coupon', function($query) use ($userId) {
            $query->forMerchant($userId);
        })
        ->where('code', $code)
        ->where('is_used', false)
        ->first();
    }

    /**
     * Get usage count for a coupon
     */
    public function getUsageCount(int $couponId): int
    {
        return CouponUsage::forCoupon($couponId)->count();
    }

    /**
     * Get usage count for a coupon by a specific customer
     */
    public function getUsageCountByCustomer(int $couponId, int $customerId): int
    {
        return CouponUsage::forCoupon($couponId)
            ->forCustomer($customerId)
            ->count();
    }

    /**
     * Check if a coupon code is already used by a customer
     */
    public function isCodeUsedByCustomer(int $couponCodeId, int $customerId): bool
    {
        return CouponUsage::where('coupon_code_id', $couponCodeId)
            ->forCustomer($customerId)
            ->exists();
    }

    /**
     * Create usage record
     */
    public function createUsage(array $data): CouponUsage
    {
        return CouponUsage::create($data);
    }

    /**
     * Get coupon codes for a coupon
     */
    public function getCodesForCoupon(int $couponId, ?int $limit = null): Collection
    {
        $query = CouponCode::where('coupon_id', $couponId)
            ->orderBy('created_at', 'desc');
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->get();
    }

    /**
     * Get usage statistics for a coupon
     */
    public function getUsageStats(int $couponId): array
    {
        $coupon = Coupon::find($couponId);
        
        if (!$coupon) {
            return [];
        }

        $totalUsage = $this->getUsageCount($couponId);
        
        $stats = [
            'total_usage' => $totalUsage,
            'unique_customers' => CouponUsage::forCoupon($couponId)
                ->distinct('customer_id')
                ->count('customer_id'),
        ];

        if ($coupon->mechanism && $coupon->mechanism->equals(CouponMechanism::MAX_USAGE)) {
            $stats['remaining'] = $coupon->max_usage 
                ? max(0, $coupon->max_usage - $totalUsage) 
                : null;
            $stats['limit'] = $coupon->max_usage;
        }

        if ($coupon->mechanism && $coupon->mechanism->equals(CouponMechanism::ONE_TIME_USAGE)) {
            $totalCodes = $coupon->codes()->count();
            $usedCodes = $coupon->codes()->where('is_used', true)->count();
            $stats['total_codes'] = $totalCodes;
            $stats['used_codes'] = $usedCodes;
            $stats['remaining'] = max(0, $totalCodes - $usedCodes);
        }

        return $stats;
    }

    /**
     * Bulk create coupon codes
     */
    public function bulkCreateCodes(int $couponId, array $codes): array
    {
        $created = [];
        
        foreach ($codes as $code) {
            $created[] = CouponCode::create([
                'coupon_id' => $couponId,
                'code' => $code,
                'is_used' => false,
            ]);
        }
        
        return $created;
    }

    /**
     * Mark code as used
     */
    public function markCodeAsUsed(CouponCode $code): bool
    {
        return $code->update(['is_used' => true]);
    }
}
