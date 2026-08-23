<?php

namespace App\Observers;

use App\Models\Settings;
use App\Services\Cache\CacheKeys;
use App\Services\MerchantSettingsService;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * PR-09: single write/invalidation path for merchant bill Settings cache.
 */
class SettingsObserver
{
    public function __construct(
        private readonly MerchantSettingsService $settingsCache,
    ) {}

    public function created(Settings $settings): void
    {
        $this->putSafely($settings, 'put');
    }

    public function updated(Settings $settings): void
    {
        $this->putSafely($settings, 'put');
    }

    public function deleted(Settings $settings): void
    {
        $this->forgetSafely($settings);
    }

    private function putSafely(Settings $settings, string $operation): void
    {
        try {
            $this->settingsCache->putSettings($settings);
        } catch (Throwable $e) {
            Log::warning('merchant_settings cache rewrite failed after settings mutation', [
                'domain' => 'merchant_settings',
                'key' => CacheKeys::merchantSettings($settings->user_id),
                'operation' => $operation,
                'reason' => 'exception',
                'exception' => $e::class,
            ]);
        }
    }

    private function forgetSafely(Settings $settings): void
    {
        try {
            $this->settingsCache->forgetSettings($settings);
        } catch (Throwable $e) {
            Log::warning('merchant_settings cache forget failed after settings delete', [
                'domain' => 'merchant_settings',
                'key' => CacheKeys::merchantSettings($settings->user_id),
                'operation' => 'forget',
                'reason' => 'exception',
                'exception' => $e::class,
            ]);
        }
    }
}
