<?php

namespace App\Enums;

/**
 * Coupon Mechanism Types
 * 
 * Defines the different mechanisms for coupon usage limits:
 * - MAX_USAGE: Maximum total usage count across all customers
 * - MAX_CUSTOMER_USAGE: Maximum usage per individual customer
 * - ONE_TIME_USAGE: Single-use codes (one code per customer, one-time use)
 * 
 * PHP 7.4+ compatible class-based enum implementation
 */
class CouponMechanism
{
    const MAX_USAGE = 'max_usage';
    const MAX_CUSTOMER_USAGE = 'max_customer_usage';
    const ONE_TIME_USAGE = 'one_time_usage';

    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * Create instance from value
     */
    public static function from(string $value): self
    {
        if (!self::isValid($value)) {
            throw new \ValueError("Invalid coupon mechanism: {$value}");
        }
        return new self($value);
    }

    /**
     * Try to create instance from value, return null if invalid
     */
    public static function tryFrom(string $value): ?self
    {
        if (!self::isValid($value)) {
            return null;
        }
        return new self($value);
    }

    /**
     * Get the string value
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Get the string value (alias for value() for compatibility)
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Magic getter for accessing value as property (for Blade compatibility)
     */
    public function __get($name)
    {
        if ($name === 'value') {
            return $this->value();
        }
        return null;
    }

    /**
     * Check if value is valid
     */
    public static function isValid(string $value): bool
    {
        return in_array($value, [
            self::MAX_USAGE,
            self::MAX_CUSTOMER_USAGE,
            self::ONE_TIME_USAGE,
        ]);
    }

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        switch ($this->value) {
            case self::MAX_USAGE:
                return __('Max Total Usage');
            case self::MAX_CUSTOMER_USAGE:
                return __('Max Per Customer');
            case self::ONE_TIME_USAGE:
                return __('One-Time Use');
            default:
                return '';
        }
    }

    /**
     * Get description for UI explanation
     */
    public function description(): string
    {
        switch ($this->value) {
            case self::MAX_USAGE:
                return __('Total usage limit across all customers');
            case self::MAX_CUSTOMER_USAGE:
                return __('Each customer can use up to the specified limit');
            case self::ONE_TIME_USAGE:
                return __('Each code can only be used once by a single customer');
            default:
                return '';
        }
    }

    /**
     * Get all values as array
     */
    public static function values(): array
    {
        return [
            self::MAX_USAGE,
            self::MAX_CUSTOMER_USAGE,
            self::ONE_TIME_USAGE,
        ];
    }

    /**
     * Get all cases/instances
     */
    public static function cases(): array
    {
        return [
            new self(self::MAX_USAGE),
            new self(self::MAX_CUSTOMER_USAGE),
            new self(self::ONE_TIME_USAGE),
        ];
    }

    /**
     * Get all options for select dropdowns
     */
    public static function options(): array
    {
        return [
            ['value' => self::MAX_USAGE, 'label' => __('Max Total Usage')],
            ['value' => self::MAX_CUSTOMER_USAGE, 'label' => __('Max Per Customer')],
            ['value' => self::ONE_TIME_USAGE, 'label' => __('One-Time Use')],
        ];
    }

    /**
     * Get all options for select dropdowns (with descriptions)
     */
    public static function optionsWithDescriptions(): array
    {
        return [
            [
                'value' => self::MAX_USAGE,
                'label' => __('Max Total Usage'),
                'description' => __('Total usage limit across all customers'),
            ],
            [
                'value' => self::MAX_CUSTOMER_USAGE,
                'label' => __('Max Per Customer'),
                'description' => __('Each customer can use up to the specified limit'),
            ],
            [
                'value' => self::ONE_TIME_USAGE,
                'label' => __('One-Time Use'),
                'description' => __('Each code can only be used once by a single customer'),
            ],
        ];
    }

    /**
     * Compare with another instance or string
     */
    public function equals($other): bool
    {
        if ($other instanceof self) {
            return $this->value === $other->value;
        }
        if (is_string($other)) {
            return $this->value === $other;
        }
        return false;
    }

    /**
     * Static instances for convenience
     */
    public static function MAX_USAGE(): self
    {
        return new self(self::MAX_USAGE);
    }

    public static function MAX_CUSTOMER_USAGE(): self
    {
        return new self(self::MAX_CUSTOMER_USAGE);
    }

    public static function ONE_TIME_USAGE(): self
    {
        return new self(self::ONE_TIME_USAGE);
    }
}