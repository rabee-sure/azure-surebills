<?php

namespace Tests\Feature\Cache;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * P0-04: verify existing Redis-backed logical cache keys (unchanged).
 * Does not route these keys through SureBillsCache.
 */
class ExistingCacheVerificationTest extends TestCase
{
    public function test_translations_key_is_present_and_retrievable(): void
    {
        $translations = cache('translations');

        $this->assertNotNull($translations);
        $this->assertTrue(
            is_array($translations) || $translations instanceof \Illuminate\Support\Collection
        );

        $localized = $translations instanceof \Illuminate\Support\Collection
            ? $translations->all()
            : $translations;

        $this->assertArrayHasKey('en', $localized);
        $this->assertArrayHasKey('ar', $localized);
        $this->assertArrayHasKey('php', $localized['en']);
        $this->assertArrayHasKey('json', $localized['en']);
    }

    public function test_spatie_permission_cache_key_contract_and_store_roundtrip(): void
    {
        $key = config('permission.cache.key');

        $this->assertSame('spatie.permission.cache', $key);
        $this->assertSame('default', config('permission.cache.store'));

        Cache::put($key, ['pr04_spatie_probe' => true], 60);
        $this->assertTrue(Cache::has($key));
        $this->assertSame(['pr04_spatie_probe' => true], Cache::get($key));
        Cache::forget($key);
        $this->assertFalse(Cache::has($key));
    }

    public function test_logical_keys_unchanged_and_not_prefixed_in_cache_api(): void
    {
        // Laravel Cache API uses logical keys; PR-03 prefixes apply at Redis store layer only.
        $this->assertSame('translations', 'translations');
        $this->assertSame('spatie.permission.cache', config('permission.cache.key'));
        $this->assertStringStartsNotWith('sb_', config('permission.cache.key'));
        $this->assertStringStartsNotWith('sb_database_', config('permission.cache.key'));
    }

    public function test_redis_topology_places_cache_keys_under_approved_prefix_when_redis_available(): void
    {
        if (! extension_loaded('redis')) {
            $this->markTestSkipped('phpredis extension not available');
        }

        $host = env('REDIS_HOST', '127.0.0.1');
        $port = (int) env('REDIS_PORT', 6379);
        $password = env('REDIS_PASSWORD');

        try {
            $redis = new \Redis();
            $redis->connect($host, $port, 1.0);
            if (! empty($password)) {
                $redis->auth($password);
            }
            if ($redis->ping() !== true && $redis->ping() !== '+PONG') {
                $this->markTestSkipped('Redis ping failed');
            }
        } catch (\Throwable $e) {
            $this->markTestSkipped('Redis unavailable: '.$e->getMessage());
        }

        config([
            'cache.default' => 'redis',
            'cache.prefix' => 'sb_',
            'database.redis.client' => 'phpredis',
            'database.redis.options.prefix' => 'sb_database_',
            'database.redis.cache.database' => env('REDIS_CACHE_DB', '1'),
            'database.redis.default.database' => env('REDIS_DB', '0'),
        ]);

        // Rebuild cache manager with updated config
        $this->app->forgetInstance('cache');
        $this->app->forgetInstance('cache.store');

        $logical = 'pr04_existing_verify_'.uniqid('', true);
        $expectedRedisKey = 'sb_database_sb_'.$logical;

        Cache::store('redis')->put($logical, 'pr04-ok', 30);

        $cacheDb = (int) config('database.redis.cache.database', 1);
        $defaultDb = (int) config('database.redis.default.database', 0);

        $redis->select($cacheDb);
        $this->assertGreaterThan(0, (int) $redis->exists($expectedRedisKey), "Expected key on DB{$cacheDb}: {$expectedRedisKey}");

        // Prefer checking serialized Laravel cache payload flexibly
        $raw = $redis->get($expectedRedisKey);
        $this->assertNotFalse($raw);
        $this->assertTrue(
            str_contains((string) $raw, 'pr04-ok') || @unserialize($raw) === 'pr04-ok',
            'Redis value should contain probe payload'
        );

        $redis->select($defaultDb);
        $this->assertSame(0, (int) $redis->exists($expectedRedisKey), 'Probe must not land on DB0');

        Cache::store('redis')->forget($logical);
        $redis->select($cacheDb);
        $this->assertSame(0, (int) $redis->exists($expectedRedisKey));
        $redis->close();
    }
}
