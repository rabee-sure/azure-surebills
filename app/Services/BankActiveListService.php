<?php

namespace App\Services;

use App\Models\Bank;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use Illuminate\Support\Collection;

/**
 * Merchant reader for the shared banks active-list cache (PR-08).
 *
 * Payload contract must stay identical to Admin PR-07 BankService::toActiveListPayload.
 */
class BankActiveListService
{
    public function __construct(
        private readonly SureBillsCache $cache,
    ) {}

    /**
     * @return Collection<int, Bank>
     */
    public function getActiveBanks(): Collection
    {
        if (! $this->cache->enabled('banks')) {
            return $this->loadActiveBanksFromDatabase();
        }

        $versionedKey = $this->cache->versionedKey(
            'banks:active',
            CacheKeys::banksActiveVersion()
        );

        $payload = $this->cache->rememberForever(
            $versionedKey,
            fn () => self::toActiveListPayload($this->loadActiveBanksFromDatabase()),
            'banks'
        );

        return $this->hydrateActiveBanks(is_array($payload) ? $payload : []);
    }

    /**
     * Active banks ordered to match Admin listActiveBanks (sort_number, id).
     *
     * @return Collection<int, Bank>
     */
    protected function loadActiveBanksFromDatabase(): Collection
    {
        return Bank::active()
            ->orderBy('sort_number')
            ->orderBy('id')
            ->get();
    }

    /**
     * Slim shared active-list payload (no fees / relations / active flag).
     *
     * @param  Collection<int, Bank>  $banks
     * @return array<int, array{id: mixed, code: mixed, name: array, sort_number: mixed}>
     */
    public static function toActiveListPayload(Collection $banks): array
    {
        return $banks
            ->map(static function (Bank $bank): array {
                return [
                    'id' => $bank->id,
                    'code' => $bank->code,
                    'name' => $bank->getTranslations('name'),
                    'sort_number' => $bank->sort_number,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return Collection<int, Bank>
     */
    protected function hydrateActiveBanks(array $rows): Collection
    {
        $attributes = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $attrs = $row;
            if (isset($attrs['name']) && is_array($attrs['name'])) {
                $attrs['name'] = json_encode($attrs['name'], JSON_UNESCAPED_UNICODE);
            }
            $attributes[] = $attrs;
        }

        return Bank::hydrate($attributes);
    }
}
