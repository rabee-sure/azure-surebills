<?php

namespace App\Services;

use App\Models\User;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;

/**
 * Sanitized merchant profile + dropdown version bump (PR-11).
 *
 * Display/read cache only. Financial paths must keep using live Eloquent User rows.
 * No controller is wired: existing profile screens also need IBAN, fees, balances,
 * and documents, which are intentionally absent from this payload.
 */
class MerchantProfileService
{
    /**
     * Fields safe for the shared profile cache. Must stay identical to Admin.
     *
     * @var list<string>
     */
    public const PAYLOAD_FIELDS = [
        'id',
        'name',
        'email',
        'mobile',
        'mobile_verified',
        'business_name_en',
        'business_name_ar',
        'logo',
        'verified',
        'store_main_user_id',
        'source',
    ];

    public function __construct(
        private readonly SureBillsCache $cache,
    ) {}

    public function get(int $id): ?User
    {
        if (! $this->cache->enabled('merchant_profile')) {
            return $this->loadFromDatabase($id);
        }

        $payload = $this->cache->rememberForever(
            CacheKeys::merchantProfile($id),
            fn () => $this->toPayloadOrNull($this->loadFromDatabase($id)),
            'merchant_profile'
        );

        return is_array($payload) && self::isValidPayload($payload, $id)
            ? $this->hydrate($payload)
            : $this->loadFromDatabase($id);
    }

    public function put(User $user): bool
    {
        if (! $this->cache->enabled('merchant_profile')) {
            return false;
        }

        return $this->cache->put(
            CacheKeys::merchantProfile($user->id),
            self::toPayload($user)
        );
    }

    public function forget(int $id): bool
    {
        if (! $this->cache->enabled('merchant_profile')) {
            return false;
        }

        return $this->cache->forget(CacheKeys::merchantProfile($id));
    }

    public function bumpDropdown(): int
    {
        if (! $this->cache->enabled('merchant_profile')) {
            return 0;
        }

        return $this->cache->bumpVersion(CacheKeys::merchantsDropdownVersion());
    }

    protected function loadFromDatabase(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function toPayloadOrNull(?User $user): ?array
    {
        return $user ? self::toPayload($user) : null;
    }

    /**
     * @return array<string, mixed>
     */
    public static function toPayload(User $user): array
    {
        $payload = [];

        foreach (self::PAYLOAD_FIELDS as $field) {
            $payload[$field] = $user->getAttribute($field);
        }

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function hydrate(array $payload): User
    {
        return User::hydrate([$payload])->first();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private static function isValidPayload(array $payload, int $id): bool
    {
        foreach (self::PAYLOAD_FIELDS as $field) {
            if (! array_key_exists($field, $payload)) {
                return false;
            }
        }

        return (int) $payload['id'] === $id;
    }
}
