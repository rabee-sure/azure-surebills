<?php

namespace App\Services\Coupon;

use App\Models\Coupon;
use App\Models\Customer;
use App\Enums\CouponMechanism;
use App\Repositories\CouponRepository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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
    private ?RedemptionClaim $activeClaim = null;

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
     * Check whether coupon status can be toggled now.
     * Rule: only within valid period.
     */
    public function canToggleStatus(Coupon $coupon): bool
    {
        $now = now();

        if ($coupon->valid_from && $now->lt($coupon->valid_from)) {
            return false;
        }

        if ($coupon->valid_to && $now->gt($coupon->valid_to)) {
            return false;
        }

        return true;
    }

    /**
     * Toggle coupon active/inactive status.
     */
    public function toggleStatus(Coupon $coupon): array
    {
        if (!$this->canToggleStatus($coupon)) {
            return [
                'success' => false,
                'message' => __('Coupon status can only be changed within valid period'),
            ];
        }

        $updated = $this->repository->update($coupon, [
            'is_active' => !$coupon->is_active,
        ]);

        if (!$updated) {
            return [
                'success' => false,
                'message' => __('Failed to update coupon status'),
            ];
        }

        return [
            'success' => true,
            'message' => $coupon->is_active
                ? __('Coupon activated successfully')
                : __('Coupon deactivated successfully'),
        ];
    }

    /**
     * Check whether coupon can be deleted.
     * Rule: delete only if never used.
     */
    public function canDelete(Coupon $coupon): bool
    {
        return $this->repository->getUsageCount($coupon->id) === 0;
    }

    /**
     * Delete coupon safely with business rule checks.
     */
    public function deleteCouponIfUnused(Coupon $coupon): array
    {
        if (!$this->canDelete($coupon)) {
            return [
                'success' => false,
                'message' => __('Coupon cannot be deleted after it has been used'),
            ];
        }

        $deleted = $this->repository->delete($coupon);

        if (!$deleted) {
            return [
                'success' => false,
                'message' => __('Failed to delete coupon'),
            ];
        }

        return [
            'success' => true,
            'message' => __('Coupon deleted successfully'),
        ];
    }

    /**
     * Validate a coupon without applying (for pre-validation)
     * 
     * Returns discount information if valid:
     * ['success' => bool, 'discount' => ['discount_type' => 'fixed'|'percentage', 'discount_value' => float], 'coupon_id' => int, 'message' => string]
     */
    public function validateCoupon(string $code, int $customerId, int $userId): array
    {
        try {
            // Try to find as reusable coupon first
            $coupon = $this->repository->findByCode($code, $userId);
            
            if (!$coupon) {
                // Try to find as one-time code
                $couponCode = $this->repository->findCodeByCode($code, $userId);
                
                if (!$couponCode) {
                    // Log for debugging
                    Log::warning('Coupon not found', [
                        'code' => $code,
                        'user_id' => $userId,
                        'customer_id' => $customerId,
                    ]);
                    
                    return [
                        'success' => false,
                        'message' => __('Invalid coupon code'),
                    ];
                }

                $coupon = $couponCode->coupon;
            }

            // Validate without applying
            $customer = \App\Models\Customer::find($customerId);
            if (!$customer) {
                return [
                    'success' => false,
                    'message' => __('Customer not found'),
                ];
            }

            $validation = $this->validator->validate($couponCode ?? $coupon, $customer);
            
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'message' => $validation['message'],
                ];
            }

            return [
                'success' => true,
                'discount' => [
                    'discount_type' => $coupon->discount_type,
                    'discount_value' => $coupon->discount_value,
                ],
                'coupon_id' => $coupon->id,
                'message' => __('Coupon is valid'),
            ];

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
     * Lock the coupon or one-time code that this transaction will redeem.
     * Must be the first statement in the redemption transaction.
     */
    public function beginRedemption(string $code, int $userId): RedemptionClaim
    {
        if (DB::transactionLevel() < 1) {
            throw new \RuntimeException('Coupon redemption requires an open database transaction.');
        }

        if ($this->activeClaim !== null) {
            throw new \RuntimeException('A coupon redemption claim is already active for this redemption.');
        }

        try {
            $coupon = $this->repository->lockCouponByCode($code, $userId);
            $couponCode = null;

            if (!$coupon) {
                $couponCode = $this->repository->lockCodeByCode($code);
                $coupon = $couponCode ? $couponCode->coupon : null;

                if (
                    !$couponCode
                    || !$coupon
                    || (int) $coupon->user_id !== $userId
                    || !$coupon->mechanism
                    || !$coupon->mechanism->equals(CouponMechanism::ONE_TIME_USAGE)
                ) {
                    throw ValidationException::withMessages([
                        'coupon_code' => __('Invalid coupon code'),
                    ]);
                }
            }
        } catch (QueryException $exception) {
            if ($this->isLockWaitTimeout($exception)) {
                throw ValidationException::withMessages([
                    'coupon_code' => __('An error occurred while validating the coupon'),
                ]);
            }

            throw $exception;
        }

        $this->activeClaim = new RedemptionClaim($code, $userId, $coupon, $couponCode);

        return $this->activeClaim;
    }

    /**
     * Re-check the coupon rules after the mutex row is locked and the customer exists.
     */
    public function assertAvailable(RedemptionClaim $claim, Customer $customer): void
    {
        if (DB::transactionLevel() < 1) {
            throw new \RuntimeException('Coupon redemption requires an open database transaction.');
        }

        if ($this->activeClaim !== $claim || $claim->state() !== RedemptionClaim::STATE_LOCKED) {
            throw new \RuntimeException('Coupon availability can only be asserted for the locked redemption claim.');
        }

        $validation = $this->validator->validate($claim->couponCode() ?? $claim->coupon(), $customer);

        if (!$validation['valid']) {
            throw ValidationException::withMessages([
                'coupon_code' => $validation['message'],
            ]);
        }

        $claim->markAsserted((int) $customer->id);
    }

    /**
     * Insert the usage row for the asserted claim. Failures throw so the surrounding transaction rolls back.
     */
    public function recordUsage(RedemptionClaim $claim, ?string $billId = null): void
    {
        if (DB::transactionLevel() < 1) {
            throw new \RuntimeException('Coupon usage recording requires an open database transaction.');
        }

        if (
            $this->activeClaim !== $claim
            || $claim->state() !== RedemptionClaim::STATE_ASSERTED
            || $claim->customerId() === null
        ) {
            throw new \RuntimeException('Coupon usage recording requires the asserted redemption claim for the locked coupon.');
        }

        $coupon = $claim->coupon();
        $couponCode = $claim->couponCode();

        $usage = $this->repository->createUsage([
            'coupon_id' => $coupon->id,
            'coupon_code_id' => $couponCode ? $couponCode->id : null,
            'customer_id' => $claim->customerId(),
            'bill_id' => $billId,
            'used_at' => now(),
        ]);

        if ($couponCode && $coupon->mechanism && $coupon->mechanism->equals(CouponMechanism::ONE_TIME_USAGE)) {
            if (!$this->repository->markCodeAsUsedIfAvailable((int) $couponCode->id)) {
                throw new \RuntimeException('Failed to mark coupon code as used.');
            }
        }

        $claim->markRecorded();
        $this->activeClaim = null;

        Log::info('Coupon usage recorded successfully', [
            'coupon_id' => $coupon->id,
            'customer_id' => $claim->customerId(),
            'bill_id' => $billId,
            'usage_id' => $usage->id,
        ]);
    }

    /**
     * Validate and apply a coupon
     * 
     * Returns discount information if valid:
     * ['success' => bool, 'discount' => ['discount_type' => 'fixed'|'percentage', 'discount_value' => float], 'coupon_id' => int, 'message' => string]
     */
    public function validateAndApply(string $code, int $customerId, int $userId, $billId = null): array
    {
        try {
            return DB::transaction(function () use ($code, $customerId, $userId, $billId) {
                $claim = $this->beginRedemption($code, $userId);

                $customer = Customer::find($customerId);
                if (!$customer) {
                    throw ValidationException::withMessages([
                        'coupon_code' => __('Customer not found'),
                    ]);
                }

                $this->assertAvailable($claim, $customer);
                $this->recordUsage($claim, $billId === null ? null : (string) $billId);

                return [
                    'success' => true,
                    'discount' => [
                        'discount_type' => $claim->discountType(),
                        'discount_value' => $claim->discountValue(),
                    ],
                    'coupon_id' => $claim->couponId(),
                    'message' => __('Coupon applied successfully'),
                ];
            });
        } catch (ValidationException $exception) {
            return [
                'success' => false,
                'message' => collect($exception->errors())->flatten()->first() ?: __('An error occurred while validating the coupon'),
            ];
        } catch (\RuntimeException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            Log::error('Coupon validation error', [
                'code' => $code,
                'customer_id' => $customerId,
                'error' => $exception->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => __('An error occurred while validating the coupon'),
            ];
        } finally {
            $this->activeClaim = null;
        }
    }

    private function isLockWaitTimeout(QueryException $exception): bool
    {
        $driverCode = (int) ($exception->errorInfo[1] ?? 0);

        return $driverCode === 1205 || $driverCode === 1213;
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
