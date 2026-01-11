<?php

namespace App\Services\Coupon;

use App\Models\Coupon;
use App\Models\CouponCode;
use App\Enums\CouponMechanism;
use App\Repositories\CouponRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CouponService
 * 
 * Main service for coupon operations.
 * Orchestrates repository, validator, generator, and exporter services.
 */
class CouponService
{
    public function __construct()
    {
        $this->repository = new CouponRepository();
        $this->validator = new CouponValidator($this->repository);
        $this->generator = new CouponGenerator();
        $this->exporter = new CouponExporter($this->repository);
    }

    private CouponRepository $repository;
    private CouponValidator $validator;
    private CouponGenerator $generator;
    private CouponExporter $exporter;

    /**
     * Get all coupons for a merchant
     */
    public function getCoupons(int $userId, int $perPage = 15)
    {
        return $this->repository->getForMerchant($userId, $perPage);
    }

    /**
     * Get a single coupon for a merchant
     */
    public function getCoupon(int $couponId, int $userId): ?Coupon
    {
        return $this->repository->findForMerchant($couponId, $userId);
    }

    /**
     * Create a new coupon
     */
    public function createCoupon(array $data, int $userId): Coupon
    {
        // Ensure user_id is set
        $data['user_id'] = $userId;

        // Set defaults based on mechanism
        if ($data['mechanism'] === 'max_usage' || $data['mechanism'] === 'max_customer_usage') {
            // For MAX_USAGE and MAX_CUSTOMER_USAGE, code_pattern becomes the reusable code
            // If not provided, generate one
            if (empty($data['code_pattern'])) {
                $data['code_pattern'] = $this->generator->generate('{ALPHA:4}-{NUMBER:4}', 8);
            }
        }

        return $this->repository->create($data);
    }

    /**
     * Update an existing coupon
     */
    public function updateCoupon(Coupon $coupon, array $data): bool
    {
        return $this->repository->update($coupon, $data);
    }

    /**
     * Delete a coupon
     */
    public function deleteCoupon(Coupon $coupon): bool
    {
        return $this->repository->delete($coupon);
    }

    /**
     * Validate and apply a coupon
     * 
     * Returns discount information if valid:
     * ['discount_type' => 'fixed'|'percentage', 'discount_value' => float]
     */
    public function validateAndApply(string $code, int $customerId, int $userId, ?int $billId = null): array
    {
        try {
            // Try to find as reusable coupon first
            $coupon = $this->repository->findByCode($code, $userId);
            
            if (!$coupon) {
                // Try to find as one-time code
                $couponCode = $this->repository->findCodeByCode($code, $userId);
                
                if (!$couponCode) {
                    return [
                        'success' => false,
                        'message' => __('Invalid coupon code'),
                    ];
                }

                $coupon = $couponCode->coupon;
                
                // Validate and apply
                $customer = \App\Models\Customer::find($customerId);
                if (!$customer) {
                    return [
                        'success' => false,
                        'message' => __('Customer not found'),
                    ];
                }

                return $this->validator->apply($couponCode, $customer, $billId);
            }

            // For reusable coupons
            $customer = \App\Models\Customer::find($customerId);
            if (!$customer) {
                return [
                    'success' => false,
                    'message' => __('Customer not found'),
                ];
            }

            return $this->validator->apply($coupon, $customer, $billId);

        } catch (\Exception $e) {
            Log::error('Coupon validation error', [
                'code' => $code,
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => __('An error occurred while validating the coupon'),
            ];
        }
    }

    /**
     * Generate bulk codes for a coupon
     */
    public function generateBulkCodes(Coupon $coupon, int $count, string $pattern = null): array
    {
        if (!$coupon->mechanism || !$coupon->mechanism->equals(CouponMechanism::ONE_TIME_USAGE)) {
            return [
                'success' => false,
                'message' => __('Bulk code generation is only available for one-time usage coupons'),
            ];
        }

        $pattern = $pattern ?? $coupon->code_pattern ?? '{RANDOM:8}';

        // Validate pattern
        $patternValidation = $this->generator->validatePattern($pattern);
        if (!$patternValidation['valid']) {
            return [
                'success' => false,
                'message' => implode(', ', $patternValidation['errors']),
            ];
        }

        try {
            // Get existing codes to avoid duplicates
            $existingCodes = $this->repository->getCodesForCoupon($coupon->id)
                ->pluck('code')
                ->toArray();

            // Generate codes
            $codes = $this->generator->generateBulk($pattern, $count, $existingCodes);

            // Save codes
            $created = $this->repository->bulkCreateCodes($coupon->id, $codes);

            return [
                'success' => true,
                'message' => __('Successfully generated :count codes', ['count' => count($created)]),
                'codes' => $codes,
            ];

        } catch (\Exception $e) {
            Log::error('Bulk code generation error', [
                'coupon_id' => $coupon->id,
                'count' => $count,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Export coupon codes
     */
    public function exportCodes(Coupon $coupon, string $format = 'csv')
    {
        if (!$coupon->mechanism || !$coupon->mechanism->equals(CouponMechanism::ONE_TIME_USAGE)) {
            return [
                'success' => false,
                'message' => __('Export is only available for one-time usage coupons'),
            ];
        }

        try {
            if ($format === 'excel') {
                return $this->exporter->exportToExcel($coupon);
            }

            return $this->exporter->exportToCsv($coupon);

        } catch (\Exception $e) {
            Log::error('Coupon export error', [
                'coupon_id' => $coupon->id,
                'format' => $format,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => __('An error occurred while exporting codes'),
            ];
        }
    }

    /**
     * Get usage statistics for a coupon
     */
    public function getUsageStats(int $couponId, int $userId): array
    {
        $coupon = $this->repository->findForMerchant($couponId, $userId);
        
        if (!$coupon) {
            return [];
        }

        return $this->repository->getUsageStats($couponId);
    }
}
