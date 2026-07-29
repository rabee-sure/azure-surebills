<?php

namespace Tests\Feature\Cache;

use App\Services\Cache\SureBillsCache;
use Tests\TestCase;

/**
 * Lightweight feature-level checks for PR-01 control plane.
 * No HTTP domain wiring exists yet — validates config boot + flag-off safety.
 */
class SureBillsCacheConfigTest extends TestCase
{
    public function test_surebills_cache_config_is_loaded(): void
    {
        $this->assertIsArray(config('surebills_cache'));
        $this->assertArrayHasKey('enabled', config('surebills_cache'));
        $this->assertArrayHasKey('domains', config('surebills_cache'));
        $this->assertArrayHasKey('ttl', config('surebills_cache'));
        $this->assertArrayHasKey('fail_open', config('surebills_cache'));
    }

    public function test_production_safe_defaults_are_disabled(): void
    {
        // Ensure file defaults (not runtime overrides) keep the kill switch off.
        $file = require config_path('surebills_cache.php');

        $this->assertFalse($file['enabled']);
        $this->assertNotContains(true, array_values($file['domains']));
    }

    public function test_ttl_constants_match_approved_spec_seconds(): void
    {
        $ttl = config('surebills_cache.ttl');

        $this->assertSame(60, $ttl['balance']);
        $this->assertSame(600, $ttl['last_bills']);
        $this->assertSame(43200, $ttl['pending_bills']);
        $this->assertSame(86400, $ttl['coupons']);
        $this->assertSame(60, $ttl['badges']);
        $this->assertSame(120, $ttl['admin_home']);
        $this->assertSame(604800, $ttl['charts_week']);
        $this->assertSame(2592000, $ttl['charts_month']);
        $this->assertSame(86400, $ttl['charts_year']);
        $this->assertSame(86400, $ttl['transfers']);
        $this->assertSame(60, $ttl['transactions']);
    }

    public function test_remember_with_default_flags_never_uses_cache_store(): void
    {
        // Default env for testing still has master disabled via setUp file defaults
        config(['surebills_cache.enabled' => false]);

        $cache = $this->app->make(SureBillsCache::class);

        $calls = 0;
        $value = $cache->rememberForever('should-not-store', function () use (&$calls) {
            $calls++;

            return 'live';
        }, 'basic_settings');

        $this->assertSame('live', $value);
        $this->assertSame(1, $calls);
        $this->assertFalse(cache()->has('should-not-store'));
    }
}
