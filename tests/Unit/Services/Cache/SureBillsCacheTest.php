<?php

namespace Tests\Unit\Services\Cache;

use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use Tests\TestCase;

class SureBillsCacheTest extends TestCase
{
    /** @var SureBillsCache */
    protected $cache;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cache = $this->app->make(SureBillsCache::class);

        config([
            'surebills_cache.enabled' => false,
            'surebills_cache.fail_open' => true,
            'surebills_cache.domains' => [
                'basic_settings' => false,
                'banks' => false,
                'merchant_settings' => false,
                'channels' => false,
                'merchant_profile' => false,
                'customers' => false,
                'pending_bills' => false,
                'coupons' => false,
                'merchant_home' => false,
                'admin_badges' => false,
                'admin_home' => false,
                'merchant_charts' => false,
                'merchant_transactions' => false,
                'transfers' => false,
            ],
        ]);

        Cache::flush();
    }

    public function test_config_defaults_disable_all_domains(): void
    {
        // Reload from file defaults (ignore setUp overrides for this assertion path)
        $domains = require config_path('surebills_cache.php');

        $this->assertFalse($domains['enabled']);
        $this->assertTrue($domains['fail_open']);

        foreach ($domains['domains'] as $domain => $value) {
            $this->assertFalse($value, "Domain [{$domain}] must default to false");
        }
    }

    public function test_enabled_requires_master_and_domain_flags(): void
    {
        config(['surebills_cache.enabled' => false, 'surebills_cache.domains.banks' => true]);
        $this->assertFalse($this->cache->enabled('banks'));

        config(['surebills_cache.enabled' => true, 'surebills_cache.domains.banks' => false]);
        $this->assertFalse($this->cache->enabled('banks'));

        config(['surebills_cache.enabled' => true, 'surebills_cache.domains.banks' => true]);
        $this->assertTrue($this->cache->enabled('banks'));
    }

    public function test_remember_bypasses_store_when_domain_disabled(): void
    {
        config([
            'surebills_cache.enabled' => true,
            'surebills_cache.domains.banks' => false,
        ]);

        $calls = 0;
        $result = $this->cache->remember('banks-test', 60, function () use (&$calls) {
            $calls++;

            return 'from-db';
        }, 'banks');

        $this->assertSame('from-db', $result);
        $this->assertSame(1, $calls);
        $this->assertFalse(Cache::has('banks-test'));

        // Second call still executes callback (no cache population)
        $this->cache->remember('banks-test', 60, function () use (&$calls) {
            $calls++;

            return 'from-db-2';
        }, 'banks');

        $this->assertSame(2, $calls);
    }

    public function test_remember_miss_then_hit_when_domain_enabled(): void
    {
        config([
            'surebills_cache.enabled' => true,
            'surebills_cache.domains.banks' => true,
        ]);

        $calls = 0;
        $first = $this->cache->remember('banks-miss-hit', 60, function () use (&$calls) {
            $calls++;

            return ['id' => 1];
        }, 'banks');

        $second = $this->cache->remember('banks-miss-hit', 60, function () use (&$calls) {
            $calls++;

            return ['id' => 999];
        }, 'banks');

        $this->assertSame(['id' => 1], $first);
        $this->assertSame(['id' => 1], $second);
        $this->assertSame(1, $calls);
    }

    public function test_remember_forever_miss_then_hit(): void
    {
        config([
            'surebills_cache.enabled' => true,
            'surebills_cache.domains.basic_settings' => true,
        ]);

        $calls = 0;
        $first = $this->cache->rememberForever('basic-forever', function () use (&$calls) {
            $calls++;

            return ['mobile_number' => '050'];
        }, 'basic_settings');

        $second = $this->cache->rememberForever('basic-forever', function () use (&$calls) {
            $calls++;

            return ['mobile_number' => 'changed'];
        }, 'basic_settings');

        $this->assertSame(['mobile_number' => '050'], $first);
        $this->assertSame(['mobile_number' => '050'], $second);
        $this->assertSame(1, $calls);
    }

    public function test_put_and_forget_round_trip(): void
    {
        $this->assertTrue($this->cache->put('round-trip', 'value', 60));
        $this->assertSame('value', Cache::get('round-trip'));

        $this->assertTrue($this->cache->forget('round-trip'));
        $this->assertNull(Cache::get('round-trip'));
    }

    public function test_put_forever_when_ttl_null(): void
    {
        $this->assertTrue($this->cache->put('forever-key', ['a' => 1], null));
        $this->assertSame(['a' => 1], Cache::get('forever-key'));
    }

    public function test_put_returns_false_when_cache_store_rejects_write(): void
    {
        Cache::shouldReceive('put')
            ->once()
            ->with('rejected-write', 'value', 60)
            ->andReturn(false);

        $this->assertFalse($this->cache->put('rejected-write', 'value', 60));
    }

    public function test_bump_version_and_versioned_key(): void
    {
        $versionKey = CacheKeys::banksActiveVersion();
        $base = 'banks:active';

        $this->assertSame('banks:active:v1', $this->cache->versionedKey($base, $versionKey));

        $v1 = $this->cache->bumpVersion($versionKey);
        $this->assertSame(1, $v1);
        $this->assertSame('banks:active:v1', $this->cache->versionedKey($base, $versionKey));

        $v2 = $this->cache->bumpVersion($versionKey);
        $this->assertSame(2, $v2);
        $this->assertSame('banks:active:v2', $this->cache->versionedKey($base, $versionKey));
    }

    public function test_versioned_key_treats_missing_version_as_one(): void
    {
        $this->assertSame(
            'merchants:dropdown:v1',
            $this->cache->versionedKey('merchants:dropdown', CacheKeys::merchantsDropdownVersion())
        );
    }

    public function test_remember_fail_open_returns_callback_result(): void
    {
        config([
            'surebills_cache.enabled' => true,
            'surebills_cache.domains.banks' => true,
            'surebills_cache.fail_open' => true,
        ]);

        Cache::shouldReceive('remember')
            ->once()
            ->andThrow(new RuntimeException('redis down'));

        $result = $this->cache->remember('fail-open-key', 60, function () {
            return 'db-fallback';
        }, 'banks');

        $this->assertSame('db-fallback', $result);
    }

    public function test_remember_rethrows_when_fail_open_is_disabled(): void
    {
        config([
            'surebills_cache.enabled' => true,
            'surebills_cache.domains.banks' => true,
            'surebills_cache.fail_open' => false,
        ]);

        Cache::shouldReceive('remember')
            ->once()
            ->andThrow(new RuntimeException('redis down'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('redis down');

        $this->cache->remember('fail-closed-key', 60, function () {
            return 'db-fallback';
        }, 'banks');
    }

    public function test_put_and_forget_fail_open_do_not_throw(): void
    {
        config(['surebills_cache.fail_open' => true]);

        Cache::shouldReceive('put')
            ->once()
            ->andThrow(new RuntimeException('redis down'));

        $this->assertFalse($this->cache->put('x', 'y', 60));

        Cache::shouldReceive('forget')
            ->once()
            ->andThrow(new RuntimeException('redis down'));

        $this->assertFalse($this->cache->forget('x'));
    }

    public function test_put_forever_fail_open_does_not_throw(): void
    {
        config(['surebills_cache.fail_open' => true]);

        Cache::shouldReceive('forever')
            ->once()
            ->andThrow(new RuntimeException('redis down'));

        $this->assertFalse($this->cache->put('forever-key', 'value'));
    }

    public function test_bump_version_fail_open_returns_zero(): void
    {
        config(['surebills_cache.fail_open' => true]);

        Cache::shouldReceive('increment')
            ->once()
            ->andThrow(new RuntimeException('redis down'));

        $this->assertSame(0, $this->cache->bumpVersion('some:version'));
    }

    public function test_versioned_key_fail_open_uses_version_one(): void
    {
        config(['surebills_cache.fail_open' => true]);

        Cache::shouldReceive('get')
            ->once()
            ->andThrow(new RuntimeException('redis down'));

        $this->assertSame(
            'banks:active:v1',
            $this->cache->versionedKey('banks:active', CacheKeys::banksActiveVersion())
        );
    }

    public function test_cache_keys_shared_string_fixtures(): void
    {
        $this->assertSame('basic_settings', CacheKeys::basicSettings());
        $this->assertSame('bank:42', CacheKeys::bank(42));
        $this->assertSame('banks:active:version', CacheKeys::banksActiveVersion());
        $this->assertSame('banks:active:v3', CacheKeys::banksActive(3));
        $this->assertSame('merchant:9:settings', CacheKeys::merchantSettings(9));
        $this->assertSame('channel:7', CacheKeys::channel(7));
        $this->assertSame('application:8', CacheKeys::application(8));
        $this->assertSame('merchant:9:profile', CacheKeys::merchantProfile(9));
        $this->assertSame('admin:badges:pending-transfers', CacheKeys::adminBadge('pending-transfers'));
        $this->assertSame('merchant:9:ui:balance', CacheKeys::uiBalance(9));
        $this->assertSame('merchant:9:transfers:202607', CacheKeys::transfersMonth(9, '202607'));
    }

    public function test_helper_is_resolvable_from_container(): void
    {
        $resolved = $this->app->make(SureBillsCache::class);

        $this->assertInstanceOf(SureBillsCache::class, $resolved);
    }
}
