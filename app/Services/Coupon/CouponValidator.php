<?php

namespace App\Services\Coupon;

use App\Models\Coupon;
use App\Models\CouponCode;
use App\Models\Customer;
use App\Enums\CouponMechanism;
use App\Repositories\CouponRepository;

/**
 * CouponValidator Service
 * 
 * Handles all validation logic for coupon usage.
 * Validates if a coupon can be used by a specific customer.
 */
class CouponValidator
{
    protected $repository;

    public function __construct(CouponRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Validate if a coupon can be used
     * 
     * @param Coupon|CouponCode $couponOrCode
     * @param Customer $customer
     * @return array ['valid' => bool, 'message' => string]
     */
    public function validate($couponOrCode, Customer $customer): array
    {
        // If it's a code, get the coupon
        if ($couponOrCode instanceof CouponCode) {
            $coupon = $couponOrCode->coupon;
            $couponCode = $couponOrCode;
        } else {
            $coupon = $couponOrCode;
            $couponCode = null;
        }

        // Check if coupon is active
        if (!$coupon->is_active) {
            return [
                'valid' => false,
                'message' => __('Coupon is not active'),
            ];
        }

        // Check date validity
        if ($coupon->is_expired) {
            return [
                'valid' => false,
                'message' => __('Coupon has expired'),
            ];
        }

        $now = now();
        if ($coupon->valid_from && $now->lt($coupon->valid_from)) {
            return [
                'valid' => false,
                'message' => __('Coupon is not yet valid'),
            ];
        }

        // Validate based on mechanism
        if (!$coupon->mechanism) {
            return [
                'valid' => false,
                'message' => __('Invalid coupon mechanism'),
            ];
        }

        $mechanismValue = $coupon->mechanism->value();
        switch ($mechanismValue) {
            case CouponMechanism::MAX_USAGE:
                return $this->validateMaxUsage($coupon, $customer);

            case CouponMechanism::MAX_CUSTOMER_USAGE:
                return $this->validateMaxCustomerUsage($coupon, $customer);

            case CouponMechanism::ONE_TIME_USAGE:
                if (!$couponCode) {
                    return [
                        'valid' => false,
                        'message' => __('Invalid coupon code'),
                    ];
                }
                return $this->validateOneTimeUsage($coupon, $couponCode, $customer);

            default:
                return [
                    'valid' => false,
                    'message' => __('Invalid coupon mechanism'),
                ];
        }
    }

    /**
     * Validate MAX_USAGE mechanism
     */
    private function validateMaxUsage(Coupon $coupon, Customer $customer): array
    {
        $totalUsage = $this->repository->getUsageCount($coupon->id);

        if ($coupon->max_usage && $totalUsage >= $coupon->max_usage) {
            return [
                'valid' => false,
                'message' => __('Coupon usage limit has been reached'),
            ];
        }

        return ['valid' => true, 'message' => __('Coupon is valid')];
    }

    /**
     * Validate MAX_CUSTOMER_USAGE mechanism
     */
    private function validateMaxCustomerUsage(Coupon $coupon, Customer $customer): array
    {
        $customerUsage = $this->repository->getUsageCountByCustomer($coupon->id, $customer->id);

        if ($coupon->max_customer_usage && $customerUsage >= $coupon->max_customer_usage) {
            return [
                'valid' => false,
                'message' => __('You have reached the maximum usage limit for this coupon'),
            ];
        }

        return ['valid' => true, 'message' => __('Coupon is valid')];
    }

    /**
     * Validate ONE_TIME_USAGE mechanism
     */
    private function validateOneTimeUsage(Coupon $coupon, CouponCode $couponCode, Customer $customer): array
    {
        // Check if code is already used
        if ($couponCode->is_used) {
            return [
                'valid' => false,
                'message' => __('This coupon code has already been used'),
            ];
        }

        // Check if this customer already used this code
        if ($this->repository->isCodeUsedByCustomer($couponCode->id, $customer->id)) {
            return [
                'valid' => false,
                'message' => __('You have already used this coupon code'),
            ];
        }

        return ['valid' => true, 'message' => __('Coupon is valid')];
    }

    /**
     * Apply coupon and record usage
     * 
     * @param Coupon|CouponCode $couponOrCode
     * @param Customer $customer
     * @param int|null $billId
     * @return array ['success' => bool, 'discount' => array, 'message' => string]
     */
    public function apply($couponOrCode, Customer $customer, ?int $billId = null): array
    {
        $validation = $this->validate($couponOrCode, $customer);

        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message'],
            ];
        }

        // Get the coupon
        $coupon = $couponOrCode instanceof CouponCode 
            ? $couponOrCode->coupon 
            : $couponOrCode;
        
        $couponCode = $couponOrCode instanceof CouponCode 
            ? $couponOrCode 
            : null;

        // Record usage
        $usage = $this->repository->createUsage([
            'coupon_id' => $coupon->id,
            'coupon_code_id' => $couponCode ? $couponCode->id : null,
            'customer_id' => $customer->id,
            'bill_id' => $billId,
            'used_at' => now(),
        ]);

        // Mark code as used if it's ONE_TIME_USAGE
        if ($couponCode && $coupon->mechanism && $coupon->mechanism->equals(CouponMechanism::ONE_TIME_USAGE)) {
            $this->repository->markCodeAsUsed($couponCode);
        }

        return [
            'success' => true,
            'discount' => [
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount_value,
            ],
            'message' => __('Coupon applied successfully'),
        ];
    }
}
