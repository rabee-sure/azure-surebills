<?php

namespace App\Services;

use App\Models\BasicSettings;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;

class BasicSettingsService
{
    public function __construct(
        private readonly SureBillsCache $cache,
    ) {}

    public function getSettings(): array
    {
        if (! $this->cache->enabled('basic_settings')) {
            return $this->loadSettingsFromDatabase();
        }

        return $this->cache->rememberForever(
            CacheKeys::basicSettings(),
            fn () => $this->loadSettingsFromDatabase(),
            'basic_settings'
        );
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->getSettings()[$key] ?? $default;
    }

    /**
     * Existing basic_settings DB read (unchanged pluck contract).
     *
     * @return array<string, mixed>
     */
    protected function loadSettingsFromDatabase(): array
    {
        return BasicSettings::query()
            ->pluck('value', 'key')
            ->toArray();
    }
}
