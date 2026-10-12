<?php

namespace App\Services;

use App\Models\Application;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;

/**
 * Merchant-owned Application fee-configuration cache for UI/display (PR-10).
 *
 * Financial calculations must not use this service; they read live Eloquent/DB models.
 */
class ApplicationFeeConfigService
{
    public function __construct(
        private readonly SureBillsCache $cache,
    ) {}

    public function get(int $id): ?Application
    {
        if (! $this->cache->enabled('channels')) {
            return $this->loadFromDatabase($id);
        }

        $payload = $this->cache->rememberForever(
            CacheKeys::application($id),
            fn () => $this->toPayloadOrNull($this->loadFromDatabase($id)),
            'channels'
        );

        return is_array($payload) && self::isValidPayload($payload, $id)
            ? $this->hydrate($payload)
            : $this->loadFromDatabase($id);
    }

    public function put(Application $application): bool
    {
        if (! $this->cache->enabled('channels')) {
            return false;
        }

        return $this->cache->put(
            CacheKeys::application($application->id),
            self::toPayload($application)
        );
    }

    public function forget(int $id): bool
    {
        if (! $this->cache->enabled('channels')) {
            return false;
        }

        return $this->cache->forget(CacheKeys::application($id));
    }

    protected function loadFromDatabase(int $id): ?Application
    {
        return Application::find($id);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function toPayloadOrNull(?Application $application): ?array
    {
        return $application ? self::toPayload($application) : null;
    }

    /**
     * @return array<string, mixed>
     */
    public static function toPayload(Application $application): array
    {
        return [
            'id' => $application->id,
            'user_id' => $application->user_id,
            'channel_id' => $application->channel_id,
            'mada_fixed' => self::decimal($application->mada_fixed),
            'mada_percentage' => self::decimal($application->mada_percentage),
            'credit_cards_fixed' => self::decimal($application->credit_cards_fixed),
            'credit_cards_percentage' => self::decimal($application->credit_cards_percentage),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function hydrate(array $payload): Application
    {
        return Application::hydrate([$payload])->first();
    }

    private static function decimal($value): ?string
    {
        return $value === null ? null : (string) $value;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private static function isValidPayload(array $payload, int $id): bool
    {
        foreach ([
            'id',
            'user_id',
            'channel_id',
            'mada_fixed',
            'mada_percentage',
            'credit_cards_fixed',
            'credit_cards_percentage',
        ] as $field) {
            if (! array_key_exists($field, $payload)) {
                return false;
            }
        }

        return (int) $payload['id'] === $id;
    }
}
