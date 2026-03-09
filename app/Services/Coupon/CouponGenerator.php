<?php

namespace App\Services\Coupon;

use Illuminate\Support\Str;

/**
 * CouponGenerator Service
 * 
 * Handles generation of coupon codes based on patterns.
 */
class CouponGenerator
{
    /**
     * Generate a single code based on pattern
     * 
     * Pattern placeholders:
     * - {RANDOM}: Random alphanumeric characters
     * - {UUID}: UUID v4
     * - {NUMBER}: Random numeric digits
     * - {ALPHA}: Random alphabetic characters
     * 
     * @param string $pattern
     * @param int $length Length for random placeholders (default: 8)
     * @return string
     */
    public function generate(string $pattern, int $length = 8): string
    {
        $code = $pattern;

        // Replace {RANDOM} with alphanumeric characters
        $code = preg_replace_callback('/\{RANDOM(?::(\d+))?\}/', function($matches) use ($length) {
            $len = isset($matches[1]) ? (int)$matches[1] : $length;
            return Str::upper(Str::random($len));
        }, $code);

        // Replace {UUID} with UUID
        $code = str_replace('{UUID}', Str::uuid()->toString(), $code);

        // Replace {NUMBER} with numeric digits
        $code = preg_replace_callback('/\{NUMBER(?::(\d+))?\}/', function($matches) use ($length) {
            $len = isset($matches[1]) ? (int)$matches[1] : $length;
            return $this->generateNumeric($len);
        }, $code);

        // Replace {ALPHA} with alphabetic characters
        $code = preg_replace_callback('/\{ALPHA(?::(\d+))?\}/', function($matches) use ($length) {
            $len = isset($matches[1]) ? (int)$matches[1] : $length;
            return Str::upper($this->generateAlpha($len));
        }, $code);

        return $code;
    }

    /**
     * Generate multiple unique codes
     * 
     * @param string $pattern
     * @param int $count
     * @param array $existingCodes Existing codes to avoid duplicates
     * @param int $length
     * @return array
     */
    public function generateBulk(string $pattern, int $count, array $existingCodes = [], int $length = 8): array
    {
        $codes = [];
        $attempts = 0;
        $maxAttempts = $count * 100; // Prevent infinite loops

        while (count($codes) < $count && $attempts < $maxAttempts) {
            $code = $this->generate($pattern, $length);
            
            if (!in_array($code, $codes) && !in_array($code, $existingCodes)) {
                $codes[] = $code;
            }
            
            $attempts++;
        }

        if (count($codes) < $count) {
            throw new \Exception(__('Failed to generate enough unique codes. Try a different pattern or reduce the count.'));
        }

        return $codes;
    }

    /**
     * Generate numeric string
     */
    private function generateNumeric(int $length): string
    {
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= rand(0, 9);
        }
        return $result;
    }

    /**
     * Generate alphabetic string
     */
    private function generateAlpha(int $length): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $result;
    }

    /**
     * Validate pattern syntax
     */
    public function validatePattern(string $pattern): array
    {
        $errors = [];

        // Check for balanced braces
        if (substr_count($pattern, '{') !== substr_count($pattern, '}')) {
            $errors[] = __('Pattern has unbalanced braces');
        }

        // Check for valid placeholders
        $validPlaceholders = ['RANDOM', 'UUID', 'NUMBER', 'ALPHA'];
        preg_match_all('/\{([^}]+)\}/', $pattern, $matches);
        
        foreach ($matches[1] ?? [] as $placeholder) {
            $parts = explode(':', $placeholder);
            $basePlaceholder = $parts[0];
            
            if (!in_array($basePlaceholder, $validPlaceholders)) {
                $errors[] = __('Invalid placeholder: :placeholder', ['placeholder' => $basePlaceholder]);
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
