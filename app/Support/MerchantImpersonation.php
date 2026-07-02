<?php

namespace App\Support;

final class MerchantImpersonation
{
    public static function validatePayload(?string $payload, ?string $signature): ?array
    {
        if (! $payload || ! $signature || ! config('app.bill_token')) {
            return null;
        }

        if (! hash_equals(self::sign($payload), $signature)) {
            return null;
        }

        $data = json_decode(base64_decode($payload, true) ?: '', true);
        if (! is_array($data) || ($data['exp'] ?? 0) < time()) {
            return null;
        }

        return $data;
    }

    private static function sign(string $payload): string
    {
        return hash_hmac('sha256', $payload, (string) config('app.bill_token'));
    }
}
