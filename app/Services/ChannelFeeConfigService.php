<?php

namespace App\Services;

use App\Models\Channel;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;

/**
 * Merchant UI/display reader for the shared Channel fee-configuration cache (PR-10).
 *
 * Financial calculations must not use this service; they read live Eloquent/DB models.
 */
class ChannelFeeConfigService
{
    public function __construct(
        private readonly SureBillsCache $cache,
    ) {}

    public function get(int $id): ?Channel
    {
        if (! $this->cache->enabled('channels')) {
            return $this->loadFromDatabase($id);
        }

        $payload = $this->cache->rememberForever(
            CacheKeys::channel($id),
            fn () => $this->toPayloadOrNull($this->loadFromDatabase($id)),
            'channels'
        );

        return is_array($payload) && self::isValidPayload($payload, $id)
            ? $this->hydrate($payload)
            : $this->loadFromDatabase($id);
    }

    protected function loadFromDatabase(int $id): ?Channel
    {
        return Channel::find($id);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function toPayloadOrNull(?Channel $channel): ?array
    {
        return $channel ? self::toPayload($channel) : null;
    }

    /**
     * This contract must stay identical to the Admin Channel writer.
     *
     * @return array<string, mixed>
     */
    public static function toPayload(Channel $channel): array
    {
        return [
            'id' => $channel->id,
            'user_id' => $channel->user_id,
            'name' => $channel->name,
            'mada_fixed' => self::decimal($channel->mada_fixed),
            'mada_percentage' => self::decimal($channel->mada_percentage),
            'credit_cards_fixed' => self::decimal($channel->credit_cards_fixed),
            'credit_cards_percentage' => self::decimal($channel->credit_cards_percentage),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function hydrate(array $payload): Channel
    {
        return Channel::hydrate([$payload])->first();
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
            'name',
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
