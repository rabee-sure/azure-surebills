<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\TransferController;
use App\Models\Bill;
use App\Rules\TransferBalance;
use App\Services\BasicSettingsService;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use App\Services\TransferService;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Support\Facades\Facade;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class HomeDisplayCacheTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private const LAST_BILL_FIELDS = [
        'id',
        'number',
        'customer_name',
        'fixed_total',
        'status',
        'method',
        'created_at',
        'user_id',
        'type',
    ];

    private ?Container $previousContainer = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = Facade::getFacadeApplication();
        $app = new Container;
        $app->instance('config', new Repository([
            'surebills_cache' => [
                'enabled' => true,
                'fail_open' => true,
                'log_sample_rate' => 0,
                'domains' => [
                    'merchant_home' => true,
                ],
                'ttl' => [
                    'balance' => 60,
                    'last_bills' => 600,
                    'home_totals' => 60,
                ],
            ],
        ]));
        Container::setInstance($app);
        Facade::setFacadeApplication($app);
        Facade::clearResolvedInstance('cache');
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstance('cache');
        Facade::setFacadeApplication($this->previousContainer);
        Container::setInstance($this->previousContainer);
        parent::tearDown();
    }

    public function test_flag_off_reads_balance_without_remember(): void
    {
        $cache = $this->cache(false);
        $cache->shouldReceive('remember')->never();
        $home = $this->home($cache);

        $balance = $home->balance($this->merchant(10));

        $this->assertSame(19.5, $balance);
        $this->assertSame(1, $home->balanceReads);
    }

    public function test_flag_off_reads_last_bills_without_remember(): void
    {
        $cache = $this->cache(false);
        $cache->shouldReceive('remember')->never();
        $home = $this->home($cache);

        $latest = $home->lastBills(42);

        $this->assertCount(6, $latest);
        $this->assertSame(1, $home->lastBillQueries);
        $this->assertNotInstanceOf(Bill::class, $latest->first());
    }

    public function test_flag_off_reads_totals_without_remember(): void
    {
        $cache = $this->cache(false);
        $cache->shouldReceive('remember')->never();
        $home = $this->home($cache);

        $totals = $home->totals(42);

        $this->assertSame([
            'total_paid' => '10.00',
            'total_bills' => 4,
            'total_paid_bills' => 2,
        ], $totals);
        $this->assertSame(1, $home->totalsQueries);
    }

    public function test_balance_cold_cache_uses_owner_key_and_sixty_second_ttl(): void
    {
        $stored = null;
        $cache = $this->cache(true);
        $cache->shouldReceive('remember')
            ->once()
            ->with(CacheKeys::uiBalance(77), 60, Mockery::type('Closure'), 'merchant_home')
            ->andReturnUsing(function ($key, $ttl, $callback) use (&$stored) {
                $stored = $callback();

                return $stored;
            });
        $home = $this->home($cache);

        $balance = $home->balance($this->merchant(10, 77));

        $this->assertSame(19.5, $balance);
        $this->assertSame(19.5, $stored);
        $this->assertSame(1, $home->balanceReads);
        $this->assertSame('merchant:77:ui:balance', CacheKeys::uiBalance(77));
    }

    public function test_balance_warm_cache_does_not_read_the_database(): void
    {
        $cache = $this->cache(true);
        $cache->shouldReceive('remember')->once()->andReturn(8.25);
        $home = $this->home($cache);

        $balance = $home->balance($this->merchant(10, 77));

        $this->assertSame(8.25, $balance);
        $this->assertSame(0, $home->balanceReads);
    }

    public function test_last_bills_cold_cache_stores_only_display_fields(): void
    {
        $stored = null;
        $cache = $this->cache(true);
        $cache->shouldReceive('remember')
            ->once()
            ->with(CacheKeys::homeLastBills(42), 600, Mockery::type('Closure'), 'merchant_home')
            ->andReturnUsing(function ($key, $ttl, $callback) use (&$stored) {
                $stored = $callback();

                return $stored;
            });
        $home = $this->home($cache);

        $latest = $home->lastBills(42);

        $this->assertCount(6, $stored);
        $this->assertSame(self::LAST_BILL_FIELDS, array_keys($stored[0]));
        $this->assertSame(['bill', 'debit_note', 'credit_note', 'bill', 'bill', 'bill'], array_column($stored, 'type'));
        $this->assertSame('3.00', $stored[2]['fixed_total']);
        $this->assertSame('visa', $stored[2]['method']);
        $this->assertSame('9.00', $stored[0]['fixed_total']);
        $this->assertSame('mada', $stored[0]['method']);
        $this->assertSame(1, $home->lastBillQueries);
        $this->assertCount(6, $latest);
        $this->assertInstanceOf(Bill::class, $latest->first());
        $this->assertInstanceOf(UrlRoutable::class, $latest->first());
        $this->assertSame('id-0', $latest->first()->getRouteKey());
        $this->assertSame('credit_note', $latest->get(2)->type);
        $this->assertSame('id-2', $latest->get(2)->id);
        $this->assertSame('2026-10-05 12:00:00', $latest->first()->created_at->format('Y-m-d H:i:s'));
    }

    public function test_last_bills_warm_cache_does_not_query_and_rebuilds_route_models(): void
    {
        $cache = $this->cache(true);
        $cache->shouldReceive('remember')->once()->andReturn([$this->sampleRows()[2]]);
        $home = $this->home($cache);

        $latest = $home->lastBills(42);

        $this->assertSame(0, $home->lastBillQueries);
        $this->assertCount(1, $latest);
        $this->assertSame('id-2', $latest->first()->getRouteKey());
        $this->assertSame('credit_note', $latest->first()->type);
    }

    public function test_totals_cold_cache_uses_owner_key_and_sixty_second_ttl(): void
    {
        $stored = null;
        $cache = $this->cache(true);
        $cache->shouldReceive('remember')
            ->once()
            ->with(CacheKeys::homeTotals(42), 60, Mockery::type('Closure'), 'merchant_home')
            ->andReturnUsing(function ($key, $ttl, $callback) use (&$stored) {
                $stored = $callback();

                return $stored;
            });
        $home = $this->home($cache);

        $totals = $home->totals(42);

        $this->assertSame([
            'total_paid',
            'total_bills',
            'total_paid_bills',
        ], array_keys($stored));
        $this->assertSame($stored, $totals);
        $this->assertSame(1, $home->totalsQueries);
    }

    public function test_totals_warm_cache_does_not_query(): void
    {
        $cache = $this->cache(true);
        $cache->shouldReceive('remember')->once()->andReturn([
            'total_paid' => '1.00',
            'total_bills' => 8,
            'total_paid_bills' => 3,
            'ignored' => 1,
        ]);
        $home = $this->home($cache);

        $totals = $home->totals(42);

        $this->assertSame([
            'total_paid' => '1.00',
            'total_bills' => 8,
            'total_paid_bills' => 3,
        ], $totals);
        $this->assertSame(0, $home->totalsQueries);
    }

    public function test_redis_failure_returns_the_database_balance(): void
    {
        $store = Mockery::mock();
        $store->shouldReceive('remember')->once()->andThrow(new RuntimeException('redis down'));
        Facade::getFacadeApplication()->instance('cache', $store);
        $home = $this->home(new SureBillsCache);

        $balance = $home->balance($this->merchant(10, 77));

        $this->assertSame(19.5, $balance);
        $this->assertSame(1, $home->balanceReads);
    }

    public function test_merchant_isolation_uses_different_owner_keys(): void
    {
        $keys = [];
        $cache = $this->cache(true);
        $cache->shouldReceive('remember')->twice()->andReturnUsing(function ($key, $ttl, $callback) use (&$keys) {
            $keys[] = $key;

            return $callback();
        });
        $home = $this->home($cache);

        $home->balance($this->merchant(10, 77));
        $home->balance($this->merchant(88));

        $this->assertSame('merchant:77:ui:balance', $keys[0]);
        $this->assertSame('merchant:88:ui:balance', $keys[1]);
        $this->assertNotSame(CacheKeys::uiBalance(77), CacheKeys::uiBalance(88));
        $this->assertNotSame(CacheKeys::homeLastBills(77), CacheKeys::homeLastBills(88));
        $this->assertNotSame(CacheKeys::homeTotals(77), CacheKeys::homeTotals(88));
    }

    public function test_display_balance_read_is_the_live_accessor_only(): void
    {
        $body = $this->methodBody(
            file_get_contents(dirname(__DIR__, 4).'/app/Http/Controllers/HomeController.php'),
            'readDisplayBalance'
        );

        $this->assertStringContainsString('return $user->balance;', $body);
        $this->assertStringNotContainsString('remember', $body);
        $this->assertStringNotContainsString('CacheKeys', $body);
    }

    public function test_last_bills_query_keeps_the_union_limit_and_column_map(): void
    {
        $body = $this->methodBody(
            file_get_contents(dirname(__DIR__, 4).'/app/Http/Controllers/HomeController.php'),
            'queryLastBills'
        );

        $this->assertStringContainsString('payment_way as method', $body);
        $this->assertStringContainsString('amount as fixed_total', $body);
        $this->assertStringContainsString("'credit_note' as type", $body);
        $this->assertStringContainsString("THEN 'bill' ELSE 'debit_note'", $body);
        $this->assertStringContainsString("orderBy('created_at', 'desc')->take(6)", $body);
    }

    public function test_totals_query_keeps_paid_only_filters(): void
    {
        $body = $this->methodBody(
            file_get_contents(dirname(__DIR__, 4).'/app/Http/Controllers/HomeController.php'),
            'queryDisplayTotals'
        );

        $this->assertSame(2, substr_count($body, "where('status', 'paid')"));
        $this->assertStringContainsString("sum('total')", $body);
        $this->assertStringContainsString('->count()', $body);
        $this->assertStringNotContainsString('paid_cash', $body);
    }

    public function test_pos_redirect_happens_before_widget_reads(): void
    {
        $body = $this->methodBody(
            file_get_contents(dirname(__DIR__, 4).'/app/Http/Controllers/HomeController.php'),
            'index'
        );
        $pos = strpos($body, "source == 'pos'");
        $balance = strpos($body, 'displayBalance');

        $this->assertNotFalse($pos);
        $this->assertNotFalse($balance);
        $this->assertLessThan($balance, $pos);
    }

    public function test_home_totals_ttl_defaults_to_sixty_seconds(): void
    {
        $source = file_get_contents(dirname(__DIR__, 4).'/config/surebills_cache.php');

        $this->assertStringContainsString("'home_totals' => 60", $source);
        $this->assertStringContainsString("'balance' => 60", $source);
        $this->assertStringContainsString("'last_bills' => 600", $source);
        $this->assertStringContainsString("'merchant_home' => env('CACHE_DOMAIN_MERCHANT_HOME', false)", $source);
    }

    public function test_transfer_request_store_and_balance_rule_do_not_read_home_display_caches(): void
    {
        $root = dirname(__DIR__, 4);
        $forbidden = [
            'ui:balance',
            'home:last_bills',
            'home:totals',
            'merchant_home',
            'CacheKeys::uiBalance',
            'CacheKeys::homeLastBills',
            'CacheKeys::homeTotals',
        ];
        $sources = [
            'TransferController::request' => $this->methodBody(
                file_get_contents($root.'/app/Http/Controllers/TransferController.php'),
                'request'
            ),
            'TransferController::store' => $this->methodBody(
                file_get_contents($root.'/app/Http/Controllers/TransferController.php'),
                'store'
            ),
            'TransferService::makeTransfer' => $this->methodBody(
                file_get_contents($root.'/app/Services/TransferService.php'),
                'makeTransfer'
            ),
            'TransferBalance' => file_get_contents($root.'/app/Rules/TransferBalance.php'),
        ];

        foreach ($sources as $name => $body) {
            foreach ($forbidden as $needle) {
                $this->assertStringNotContainsString($needle, $body, $name.' must not reference '.$needle);
            }
        }

        $this->assertTrue(method_exists(TransferController::class, 'request'));
        $this->assertTrue(method_exists(TransferController::class, 'store'));
        $this->assertTrue(method_exists(TransferService::class, 'makeTransfer'));
        $this->assertTrue(method_exists(TransferBalance::class, 'passes'));
    }

    public function test_user_balance_accessor_is_not_cached(): void
    {
        $source = file_get_contents(dirname(__DIR__, 4).'/app/Models/User.php');
        $body = $this->methodBody($source, 'getBalanceAttribute');

        $this->assertStringNotContainsString('SureBillsCache', $body);
        $this->assertStringNotContainsString('CacheKeys', $body);
        $this->assertStringNotContainsString('merchant_home', $body);
        $this->assertStringContainsString('myBalance', $body);
    }

    private function cache(bool $enabled): SureBillsCache
    {
        $cache = Mockery::mock(SureBillsCache::class);
        $cache->shouldReceive('enabled')->andReturn($enabled);

        return $cache;
    }

    private function home(SureBillsCache $cache): HomeController
    {
        return new class(Mockery::mock(BasicSettingsService::class), $cache) extends HomeController
        {
            public int $balanceReads = 0;

            public int $lastBillQueries = 0;

            public int $totalsQueries = 0;

            public function balance($user)
            {
                return $this->displayBalance($user);
            }

            public function lastBills($ownerId)
            {
                return $this->displayLastBills($ownerId);
            }

            public function totals($ownerId)
            {
                return $this->displayTotals($ownerId);
            }

            protected function readDisplayBalance($user)
            {
                $this->balanceReads++;

                return 19.5;
            }

            protected function queryLastBills($ownerId)
            {
                $this->lastBillQueries++;

                return collect(array_map(function ($row) {
                    return (object) $row;
                }, $this->sampleRows()));
            }

            protected function queryDisplayTotals($ownerId): array
            {
                $this->totalsQueries++;

                return [
                    'total_paid' => '10.00',
                    'total_bills' => 4,
                    'total_paid_bills' => 2,
                ];
            }

            private function sampleRows(): array
            {
                $rows = [];
                foreach (['bill', 'debit_note', 'credit_note', 'bill', 'bill', 'bill'] as $index => $type) {
                    $rows[] = [
                        'id' => 'id-'.$index,
                        'number' => 'N'.$index,
                        'customer_name' => 'Customer '.$index,
                        'fixed_total' => $type === 'credit_note' ? '3.00' : '9.00',
                        'status' => 'paid',
                        'method' => $type === 'credit_note' ? 'visa' : 'mada',
                        'created_at' => '2026-10-05 12:00:0'.$index,
                        'user_id' => 42,
                        'type' => $type,
                    ];
                }

                return $rows;
            }
        };
    }

    private function merchant(int $id, ?int $ownerId = null): object
    {
        return (object) [
            'id' => $id,
            'store_main_user_id' => $ownerId,
        ];
    }

    private function sampleRows(): array
    {
        return [
            2 => [
                'id' => 'id-2',
                'number' => 'N2',
                'customer_name' => 'Customer 2',
                'fixed_total' => '3.00',
                'status' => 'paid',
                'method' => 'visa',
                'created_at' => '2026-10-05 12:00:02',
                'user_id' => 42,
                'type' => 'credit_note',
            ],
        ];
    }

    private function methodBody(string $source, string $method): string
    {
        $start = strpos($source, 'function '.$method);
        $this->assertNotFalse($start, $method);
        $next = strlen($source);
        foreach (["\n    public function ", "\n    protected function ", "\n    private function "] as $marker) {
            $candidate = strpos($source, $marker, $start + 10);
            if ($candidate !== false && $candidate < $next) {
                $next = $candidate;
            }
        }

        return substr($source, $start, $next - $start);
    }
}
