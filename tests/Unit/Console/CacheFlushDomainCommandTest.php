<?php

namespace Tests\Unit\Console;

use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Mockery;
use Mockery\MockInterface;
use ReflectionProperty;
use RuntimeException;
use Tests\TestCase;

class CacheFlushDomainCommandTest extends TestCase
{
    private const SECRET = 'super-secret-redis';

    private ?RecordingArrayStore $readStore = null;

    private ?Repository $isolatedCache = null;

    protected function setUp(): void
    {
        putenv('COLUMNS=200');
        parent::setUp();

        config([
            'surebills_cache.enabled' => false,
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
    }

    public function test_unknown_domain_exits_1_without_cache_calls(): void
    {
        $cache = $this->bindCache();
        $this->rejectCacheCalls($cache);

        $this->artisan('cache:flush-domain', [
            'domain' => 'not-a-domain',
            '--execute' => true,
        ])->expectsOutputToContain('Unknown domain [not-a-domain]')
            ->assertExitCode(1)
            ->run();
    }

    public function test_unsupported_domains_exit_1_without_cache_calls(): void
    {
        foreach (['coupons', 'merchant_charts', 'merchant_transactions'] as $domain) {
            $cache = $this->bindCache();
            $cache->shouldNotReceive('forget');
            $cache->shouldNotReceive('bumpVersion');

            $this->artisan('cache:flush-domain', [
                'domain' => $domain,
                '--execute' => true,
            ])->expectsOutputToContain('Safe domain-wide invalidation is not supported')
                ->assertExitCode(1)
            ->run();
        }
    }

    public function test_missing_or_conflicting_identifiers_exit_1_without_cache_calls(): void
    {
        $cases = [
            [['domain' => 'channels', '--execute' => true], 'requires --id'],
            [['domain' => 'transfers', '--execute' => true], 'requires --user'],
            [['domain' => 'banks', '--user' => '4', '--execute' => true], 'does not accept --user'],
            [['domain' => 'channels', '--id' => '4', '--user' => '5', '--execute' => true], 'does not accept --user'],
            [['domain' => 'basic_settings', '--id' => '4', '--execute' => true], 'does not accept --id'],
            [['domain' => 'channels', '--id' => 'abc', '--execute' => true], 'positive integer'],
        ];

        foreach ($cases as [$parameters, $message]) {
            $cache = $this->bindCache();
            $cache->shouldNotReceive('forget');
            $cache->shouldNotReceive('bumpVersion');

            $this->artisan('cache:flush-domain', $parameters)
                ->expectsOutputToContain($message)
                ->assertExitCode(1)
            ->run();
        }
    }

    public function test_dry_run_lists_operations_and_target_without_cache_calls(): void
    {
        $this->approveTarget();
        $cache = $this->bindCache();
        $this->rejectCacheCalls($cache);

        $this->artisan('cache:flush-domain', ['domain' => 'basic_settings'])
            ->expectsOutputToContain('mode: dry-run')
            ->expectsOutputToContain('forget basic_settings')
            ->expectsOutputToContain('redis host: 127.0.0.1')
            ->expectsOutputToContain('redis port: 6379')
            ->expectsOutputToContain('redis database: 1')
            ->expectsOutputToContain('cache prefix: sb_')
            ->expectsOutputToContain('redis prefix: sb_database_')
            ->expectsOutputToContain('cache connection: cache')
            ->expectsOutputToContain('Dry-run only')
            ->doesntExpectOutputToContain(self::SECRET)
            ->doesntExpectOutputToContain('bypass Redis')
            ->assertExitCode(0)
            ->run();
    }

    public function test_basic_settings_execute_forgets_only_that_key(): void
    {
        $this->approveTarget();
        $cache = $this->bindCache();
        $cache->shouldReceive('forget')->once()->with(CacheKeys::basicSettings())->andReturn(true);
        $cache->shouldNotReceive('bumpVersion');
        $this->artisan('cache:flush-domain', [
            'domain' => 'basic_settings',
            '--execute' => true,
        ])->expectsOutputToContain('ok forget basic_settings')
            ->doesntExpectOutputToContain('already-absent')
            ->doesntExpectOutputToContain(self::SECRET)
            ->assertExitCode(0)
            ->run();
    }

    public function test_version_bump_of_two_succeeds(): void
    {
        $this->approveTarget();
        $cache = $this->bindCache();
        $cache->shouldReceive('bumpVersion')->once()->with(CacheKeys::banksActiveVersion())->andReturn(2);
        $cache->shouldNotReceive('forget');
        $this->artisan('cache:flush-domain', [
            'domain' => 'banks',
            '--execute' => true,
        ])->expectsOutputToContain('ok bump banks:active:version final=2')
            ->assertExitCode(0)
            ->run();
    }

    public function test_version_bump_of_one_then_two_succeeds(): void
    {
        $this->approveTarget();
        $cache = $this->bindCache();
        $cache->shouldReceive('bumpVersion')->twice()->with(CacheKeys::banksActiveVersion())->andReturn(1, 2);
        $cache->shouldNotReceive('forget');
        $this->artisan('cache:flush-domain', [
            'domain' => 'banks',
            '--execute' => true,
        ])->expectsOutputToContain('ok bump banks:active:version final=2')
            ->doesntExpectOutputToContain('final=1')
            ->doesntExpectOutputToContain('failed bump')
            ->assertExitCode(0)
            ->run();
    }

    public function test_version_bump_of_one_twice_exits_2(): void
    {
        $this->approveTarget();
        $cache = $this->bindCache();
        $cache->shouldReceive('bumpVersion')->twice()->with(CacheKeys::banksActiveVersion())->andReturn(1, 1);
        $cache->shouldNotReceive('forget');
        $this->artisan('cache:flush-domain', [
            'domain' => 'banks',
            '--execute' => true,
        ])->expectsOutputToContain('failed bump banks:active:version')
            ->expectsOutputToContain('Failed logical keys:')
            ->expectsOutputToContain('banks:active:version')
            ->doesntExpectOutputToContain('final=1')
            ->assertExitCode(2)
            ->run();
    }

    public function test_version_bump_of_one_then_zero_exits_2_after_exactly_two_calls(): void
    {
        $this->approveTarget();
        $cache = $this->bindCache();
        $cache->shouldReceive('bumpVersion')->twice()->with(CacheKeys::banksActiveVersion())->andReturn(1, 0);
        $cache->shouldNotReceive('forget');
        $this->artisan('cache:flush-domain', [
            'domain' => 'banks',
            '--execute' => true,
        ])->expectsOutputToContain('failed bump banks:active:version')
            ->expectsOutputToContain('Bump did not reach 2.')
            ->expectsOutputToContain('Old cached payload may remain in use')
            ->expectsOutputToContain('until a later successful version bump.')
            ->expectsOutputToContain('Failed logical keys:')
            ->expectsOutputToContain('banks:active:version')
            ->doesntExpectOutputToContain('final=')
            ->assertExitCode(2)
            ->run();
    }

    public function test_version_bump_of_zero_exits_2_without_a_second_call(): void
    {
        $this->approveTarget();
        $cache = $this->bindCache();
        $cache->shouldReceive('bumpVersion')->once()->with(CacheKeys::banksActiveVersion())->andReturn(0);
        $cache->shouldNotReceive('forget');
        $this->artisan('cache:flush-domain', [
            'domain' => 'banks',
            '--execute' => true,
        ])->expectsOutputToContain('Failed logical keys:')
            ->expectsOutputToContain('banks:active:version')
            ->assertExitCode(2)
            ->run();
    }

    public function test_version_bump_exception_exits_2(): void
    {
        $this->approveTarget();
        $cache = $this->bindCache();
        $cache->shouldReceive('bumpVersion')->once()->with(CacheKeys::banksActiveVersion())
            ->andThrow(new RuntimeException('redis unavailable'));
        $cache->shouldNotReceive('forget');
        $this->artisan('cache:flush-domain', [
            'domain' => 'banks',
            '--execute' => true,
        ])->expectsOutputToContain('failed bump banks:active:version')
            ->expectsOutputToContain('Failed logical keys:')
            ->assertExitCode(2)
            ->run();
    }

    public function test_forget_true_succeeds_without_a_follow_up_read(): void
    {
        $this->approveTarget();
        $cache = $this->bindCache();
        $cache->shouldReceive('forget')->once()->with(CacheKeys::basicSettings())->andReturn(true);
        $cache->shouldNotReceive('bumpVersion');
        $this->artisan('cache:flush-domain', [
            'domain' => 'basic_settings',
            '--execute' => true,
        ])->expectsOutputToContain('ok forget basic_settings')
            ->assertExitCode(0)
            ->run();

        $this->assertSame(0, $this->readStore->reads);
    }

    public function test_forget_false_and_null_read_is_already_absent(): void
    {
        $this->approveTarget();
        $cache = $this->bindCache();
        $cache->shouldReceive('forget')->once()->with(CacheKeys::basicSettings())->andReturn(false);
        $cache->shouldNotReceive('bumpVersion');

        $this->artisan('cache:flush-domain', [
            'domain' => 'basic_settings',
            '--execute' => true,
        ])->expectsOutputToContain('ok forget basic_settings already-absent')
            ->assertExitCode(0)
            ->run();

        $this->assertSame(1, $this->readStore->reads);
    }

    public function test_forget_false_and_non_null_read_exits_2(): void
    {
        $this->approveTarget();
        $cache = $this->bindCache();
        $cache->shouldReceive('forget')->once()->with(CacheKeys::basicSettings())->andReturn(false);
        $cache->shouldNotReceive('bumpVersion');
        $this->isolatedCache->put(CacheKeys::basicSettings(), 'still-present', 60);

        $this->artisan('cache:flush-domain', [
            'domain' => 'basic_settings',
            '--execute' => true,
        ])->expectsOutputToContain('failed forget basic_settings')
            ->expectsOutputToContain('Failed logical keys:')
            ->expectsOutputToContain('basic_settings')
            ->assertExitCode(2)
            ->run();

        $this->assertSame(1, $this->readStore->reads);
    }

    public function test_follow_up_read_exception_exits_2(): void
    {
        $this->approveTarget();
        $cache = $this->bindCache();
        $cache->shouldReceive('forget')->once()->with(CacheKeys::basicSettings())->andReturn(false);
        $cache->shouldNotReceive('bumpVersion');
        $this->readStore->throwOnGet = true;

        $this->artisan('cache:flush-domain', [
            'domain' => 'basic_settings',
            '--execute' => true,
        ])->expectsOutputToContain('failed forget basic_settings')
            ->expectsOutputToContain('Failed logical keys:')
            ->assertExitCode(2)
            ->run();

        $this->assertSame(1, $this->readStore->reads);
    }

    public function test_forget_exception_exits_2_without_a_follow_up_read(): void
    {
        $this->approveTarget();
        $cache = $this->bindCache();
        $cache->shouldReceive('forget')->once()->with(CacheKeys::basicSettings())
            ->andThrow(new RuntimeException('redis unavailable'));
        $cache->shouldNotReceive('bumpVersion');
        $this->artisan('cache:flush-domain', [
            'domain' => 'basic_settings',
            '--execute' => true,
        ])->expectsOutputToContain('failed forget basic_settings')
            ->expectsOutputToContain('Failed logical keys:')
            ->assertExitCode(2)
            ->run();

        $this->assertSame(0, $this->readStore->reads);
    }

    public function test_one_failed_key_does_not_stop_the_remaining_keys(): void
    {
        $this->approveTarget();
        $cache = $this->bindCache();
        $cache->shouldReceive('forget')->once()->with(CacheKeys::adminBadge('unverified-merchants'))->andReturn(false);
        $cache->shouldReceive('forget')->once()->with(CacheKeys::adminBadge('pending-tax-invoices'))->andReturn(true);
        $cache->shouldReceive('forget')->once()->with(CacheKeys::adminBadge('pending-transfers'))->andReturn(true);
        $cache->shouldNotReceive('bumpVersion');
        $this->isolatedCache->put(CacheKeys::adminBadge('unverified-merchants'), 'stale', 60);

        $this->artisan('cache:flush-domain', [
            'domain' => 'admin_badges',
            '--execute' => true,
        ])->expectsOutputToContain('failed forget admin:badges:unverified-merchants')
            ->expectsOutputToContain('ok forget admin:badges:pending-tax-invoices')
            ->expectsOutputToContain('ok forget admin:badges:pending-transfers')
            ->expectsOutputToContain('Failed logical keys:')
            ->expectsOutputToContain('admin:badges:unverified-merchants')
            ->assertExitCode(2)
            ->run();

        $this->assertSame(1, $this->readStore->reads);
    }

    public function test_database_other_than_one_is_refused_before_cache_calls(): void
    {
        foreach ([0, '0', 2, '2', null] as $database) {
            $cache = $this->bindCache();
            $cache->shouldNotReceive('forget');
            $cache->shouldNotReceive('bumpVersion');
            $this->approveTarget(['database.redis.cache.database' => $database]);

            $this->artisan('cache:flush-domain', [
                'domain' => 'basic_settings',
                '--execute' => true,
            ])->expectsOutputToContain('expected [1]')
                ->doesntExpectOutputToContain(self::SECRET)
                ->assertExitCode(1)
            ->run();
        }
    }

    public function test_redis_url_database_zero_is_refused_and_password_is_hidden(): void
    {
        $this->approveTarget([
            'database.redis.cache.url' => 'redis://:'.self::SECRET.'@127.0.0.1:6379/0',
            'database.redis.cache.database' => 1,
        ]);
        $cache = $this->bindCache();
        $this->rejectCacheCalls($cache);

        $this->artisan('cache:flush-domain', [
            'domain' => 'basic_settings',
            '--execute' => true,
        ])->expectsOutputToContain('redis database: 0')
            ->expectsOutputToContain('expected [1]')
            ->doesntExpectOutputToContain(self::SECRET)
            ->assertExitCode(1)
            ->run();
    }

    public function test_wrong_prefixes_are_refused_before_cache_calls(): void
    {
        $overrides = [
            ['cache.prefix' => 'sb'],
            ['cache.prefix' => ''],
            ['cache.prefix' => null],
            ['cache.stores.redis.prefix' => 'other_'],
            ['database.redis.options.prefix' => 'sb_'],
            ['database.redis.options.prefix' => ''],
            ['database.redis.options.prefix' => null],
        ];

        foreach ($overrides as $override) {
            $cache = $this->bindCache();
            $cache->shouldNotReceive('forget');
            $cache->shouldNotReceive('bumpVersion');
            $this->approveTarget($override);

            $pending = $this->artisan('cache:flush-domain', [
                'domain' => 'basic_settings',
                '--execute' => true,
            ])->doesntExpectOutputToContain(self::SECRET)
                ->assertExitCode(1);

            if (array_key_exists('database.redis.options.prefix', $override)) {
                $pending->expectsOutputToContain('expected [sb_database_]');
            } else {
                $pending->expectsOutputToContain('expected [sb_]');
            }

            $pending->run();
        }
    }

    public function test_non_cache_connection_and_non_redis_driver_are_refused(): void
    {
        $cache = $this->bindCache();
        $cache->shouldNotReceive('forget');
        $cache->shouldNotReceive('bumpVersion');
        $this->approveTarget([
            'cache.stores.redis.connection' => 'default',
            'database.redis.default.password' => self::SECRET,
            'database.redis.default.url' => null,
        ]);

        $this->artisan('cache:flush-domain', [
            'domain' => 'basic_settings',
            '--execute' => true,
        ])->expectsOutputToContain('expected [cache]')
            ->doesntExpectOutputToContain(self::SECRET)
            ->assertExitCode(1)
            ->run();

        $cache = $this->bindCache();
        $cache->shouldNotReceive('forget');
        $cache->shouldNotReceive('bumpVersion');
        $this->approveTarget(['cache.default' => 'array']);

        $this->artisan('cache:flush-domain', [
            'domain' => 'basic_settings',
            '--execute' => true,
        ])->expectsOutputToContain('expected [redis]')
            ->assertExitCode(1)
            ->run();
    }

    public function test_disabled_domain_flag_still_flushes_and_warns(): void
    {
        $this->approveTarget([
            'surebills_cache.enabled' => true,
            'surebills_cache.domains.basic_settings' => false,
        ]);
        $cache = $this->bindCache();
        $cache->shouldReceive('forget')->once()->with(CacheKeys::basicSettings())->andReturn(true);
        $cache->shouldNotReceive('bumpVersion');
        $this->artisan('cache:flush-domain', [
            'domain' => 'basic_settings',
            '--execute' => true,
        ])->expectsOutputToContain('Domain flag [basic_settings] is off')
            ->expectsOutputToContain('Web reads bypass Redis')
            ->expectsOutputToContain('ok forget basic_settings')
            ->assertExitCode(0)
            ->run();
    }

    public function test_scoped_domains_touch_only_the_approved_keys(): void
    {
        $this->approveTarget();
        $cache = $this->bindCache();
        $singleForget = [
            ['channels', ['--id' => '4'], CacheKeys::channel('4')],
            ['applications', ['--id' => '4'], CacheKeys::application('4')],
            ['customers', ['--id' => '8'], CacheKeys::customer('8')],
            ['merchant_settings', ['--user' => '8'], CacheKeys::merchantSettings('8')],
            ['pending_bills', ['--user' => '8'], CacheKeys::pendingBills('8')],
            ['transfers', ['--user' => '8'], CacheKeys::transfers('8')],
        ];

        foreach ($singleForget as [$domain, $options, $key]) {
            $cache->shouldReceive('forget')->once()->with($key)->andReturn(true);

            $this->artisan('cache:flush-domain', array_merge([
                'domain' => $domain,
                '--execute' => true,
            ], $options))->expectsOutputToContain('ok forget '.$key)
                ->assertExitCode(0)
                ->run();
        }

        $cache->shouldReceive('forget')->once()->with(CacheKeys::uiBalance('8'))->andReturn(true);
        $cache->shouldReceive('forget')->once()->with(CacheKeys::homeLastBills('8'))->andReturn(true);
        $cache->shouldReceive('forget')->once()->with(CacheKeys::homeTotals('8'))->andReturn(true);

        $this->artisan('cache:flush-domain', [
            'domain' => 'merchant_home',
            '--user' => '8',
            '--execute' => true,
        ])->expectsOutputToContain('ok forget merchant:8:ui:balance')
            ->expectsOutputToContain('ok forget merchant:8:home:last_bills')
            ->expectsOutputToContain('ok forget merchant:8:home:totals')
            ->doesntExpectOutputToContain('charts')
            ->assertExitCode(0)
            ->run();

        $cache->shouldReceive('bumpVersion')->once()->with(CacheKeys::banksActiveVersion())->andReturn(3);
        $cache->shouldReceive('forget')->once()->with(CacheKeys::bank('4'))->andReturn(true);

        $this->artisan('cache:flush-domain', [
            'domain' => 'banks',
            '--id' => '4',
            '--execute' => true,
        ])->expectsOutputToContain('ok bump banks:active:version final=3')
            ->expectsOutputToContain('ok forget bank:4')
            ->assertExitCode(0)
            ->run();

        $cache->shouldReceive('bumpVersion')->once()->with(CacheKeys::merchantsDropdownVersion())->andReturn(2);
        $cache->shouldReceive('forget')->once()->with(CacheKeys::merchantProfile('8'))->andReturn(true);

        $this->artisan('cache:flush-domain', [
            'domain' => 'merchant_profile',
            '--user' => '8',
            '--execute' => true,
        ])->expectsOutputToContain('ok bump merchants:dropdown:version final=2')
            ->expectsOutputToContain('ok forget merchant:8:profile')
            ->assertExitCode(0)
            ->run();

        $this->artisan('cache:flush-domain', ['domain' => 'admin_home'])
            ->expectsOutputToContain('bump admin:home:version')
            ->expectsOutputToContain('forget admin:home:yearly:sum_commission_vat')
            ->expectsOutputToContain('forget admin:home:yearly:sum_amount_transfers')
            ->expectsOutputToContain('forget admin:home:yearly:sum_commission')
            ->expectsOutputToContain('forget admin:home:yearly:sum_total')
            ->expectsOutputToContain('mode: dry-run')
            ->doesntExpectOutputToContain('admin:home:v1')
            ->assertExitCode(0)
            ->run();

        $this->artisan('cache:flush-domain', ['domain' => 'admin_badges'])
            ->expectsOutputToContain('forget admin:badges:unverified-merchants')
            ->expectsOutputToContain('forget admin:badges:pending-tax-invoices')
            ->expectsOutputToContain('forget admin:badges:pending-transfers')
            ->assertExitCode(0)
            ->run();
    }

    private function bindCache(): MockInterface
    {
        $cache = Mockery::mock(SureBillsCache::class);
        $this->app->instance(SureBillsCache::class, $cache);

        return $cache;
    }

    private function rejectCacheCalls(MockInterface $cache): void
    {
        $cache->shouldNotReceive('forget');
        $cache->shouldNotReceive('bumpVersion');
        $cache->shouldNotReceive('put');
        $cache->shouldNotReceive('remember');
        $cache->shouldNotReceive('rememberForever');
        $cache->shouldNotReceive('enabled');
    }

    /**
     * Plant an array repository under the redis store name so a follow-up
     * Cache::get cannot open a Redis socket. The safety checks still read config.
     */
    private function installIsolatedCacheStore(): void
    {
        $this->readStore = new RecordingArrayStore();
        $this->isolatedCache = new Repository($this->readStore);
        $manager = $this->app->make('cache');
        $property = new ReflectionProperty($manager, 'stores');
        $stores = $property->getValue($manager);
        $stores['redis'] = $this->isolatedCache;
        $property->setValue($manager, $stores);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function approveTarget(array $overrides = []): void
    {
        $config = [
            'cache.default' => 'redis',
            'cache.stores.redis.driver' => 'redis',
            'cache.stores.redis.connection' => 'cache',
            'cache.prefix' => 'sb_',
            'database.redis.options.prefix' => 'sb_database_',
            'database.redis.cache.url' => null,
            'database.redis.cache.host' => '127.0.0.1',
            'database.redis.cache.port' => 6379,
            'database.redis.cache.database' => 1,
            'database.redis.cache.password' => self::SECRET,
            'surebills_cache.enabled' => true,
        ];

        foreach ([
            'basic_settings',
            'banks',
            'merchant_settings',
            'channels',
            'merchant_profile',
            'customers',
            'pending_bills',
            'coupons',
            'merchant_home',
            'admin_badges',
            'admin_home',
            'merchant_charts',
            'merchant_transactions',
            'transfers',
        ] as $domain) {
            $config['surebills_cache.domains.'.$domain] = true;
        }

        config(array_merge($config, $overrides));
        $this->installIsolatedCacheStore();
    }
}

/**
 * In-memory cache store used so flush tests never open a Redis socket.
 */
class RecordingArrayStore extends ArrayStore
{
    public int $reads = 0;

    public bool $throwOnGet = false;

    public function get($key)
    {
        $this->reads++;
        if ($this->throwOnGet) {
            throw new RuntimeException('read failed');
        }

        return parent::get($key);
    }
}
