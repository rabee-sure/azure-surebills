<?php

namespace App\Services\Coupon;

use App\Models\Coupon;
use App\Models\CouponCode;

/**
 * In-transaction claim for one locked coupon or one-time code.
 * Only CouponService can advance it, and only for the row it locked.
 */
class RedemptionClaim
{
    public const STATE_LOCKED = 'locked';
    public const STATE_ASSERTED = 'asserted';
    public const STATE_RECORDED = 'recorded';

    private string $code;
    private int $userId;
    private Coupon $coupon;
    private ?CouponCode $couponCode;
    private string $state = self::STATE_LOCKED;
    private ?int $customerId = null;

    public function __construct(string $code, int $userId, Coupon $coupon, ?CouponCode $couponCode)
    {
        $this->code = $code;
        $this->userId = $userId;
        $this->coupon = $coupon;
        $this->couponCode = $couponCode;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function userId(): int
    {
        return $this->userId;
    }

    public function coupon(): Coupon
    {
        return $this->coupon;
    }

    public function couponCode(): ?CouponCode
    {
        return $this->couponCode;
    }

    public function couponId(): int
    {
        return (int) $this->coupon->id;
    }

    public function discountType(): string
    {
        return (string) $this->coupon->discount_type;
    }

    public function discountValue()
    {
        return $this->coupon->discount_value;
    }

    public function customerId(): ?int
    {
        return $this->customerId;
    }

    public function state(): string
    {
        return $this->state;
    }

    public function markAsserted(int $customerId): void
    {
        if ($this->state !== self::STATE_LOCKED) {
            throw new \RuntimeException('Coupon redemption claim cannot be asserted in its current state.');
        }

        $this->customerId = $customerId;
        $this->state = self::STATE_ASSERTED;
    }

    public function markRecorded(): void
    {
        if ($this->state !== self::STATE_ASSERTED || $this->customerId === null) {
            throw new \RuntimeException('Coupon redemption claim cannot be recorded in its current state.');
        }

        $this->state = self::STATE_RECORDED;
    }
}
