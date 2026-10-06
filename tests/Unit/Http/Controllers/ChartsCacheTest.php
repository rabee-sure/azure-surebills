<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\Api\ChartsController;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class ChartsCacheTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (config('cache.default') !== 'array') {
            $this->fail('Charts cache tests must use the array cache driver.');
        }

        Cache::flush();

        config([
            'surebills_cache.enabled' => false,
            'surebills_cache.fail_open' => true,
            'surebills_cache.log_sample_rate' => 0,
            'surebills_cache.domains.merchant_charts' => false,
        ]);
    }

    public function test_master_disabled_does_not_touch_chart_cache(): void
    {
        config([
            'surebills_cache.enabled' => false,
            'surebills_cache.domains.merchant_charts' => true,
        ]);

        Cache::spy();
        $controller = $this->harness();
        $controller->results['bills_paid_amount'] = ['daily' => [1 => 4]];

        $response = $controller->billsPaidAmount($this->request(15, 'daily'));

        Cache::shouldNotHaveReceived('remember');
        $this->assertSame(1, $controller->queries['bills_paid_amount']);
        $this->assertSame(4, $response['daily']['datasets'][0]['data'][0]);
        $this->assertNull(Cache::get($this->key(15, 'bills_paid_amount', 'daily', '2026-10-04', '2026-10-10')));
    }

    public function test_domain_disabled_does_not_touch_chart_cache(): void
    {
        config([
            'surebills_cache.enabled' => true,
            'surebills_cache.domains.merchant_charts' => false,
        ]);

        Cache::spy();
        $controller = $this->harness();
        $controller->results['bills_paid_count'] = ['weekly' => [40 => 3]];

        $controller->billsPaidCount($this->request(15, 'weekly'));

        Cache::shouldNotHaveReceived('remember');
        $this->assertSame(1, $controller->queries['bills_paid_count']);
    }

    public function test_flags_on_miss_runs_aggregate_and_hit_reuses_it(): void
    {
        $this->enableCharts();
        $controller = $this->harness();
        $controller->today = $this->day('2026-10-06');
        $controller->results['bills_paid_amount'] = ['daily' => collect([1 => '5.50', 3 => 2])];

        $first = $controller->billsPaidAmount($this->request(15, 'daily'));
        $controller->results['bills_paid_amount'] = ['daily' => [1 => 99]];
        $second = $controller->billsPaidAmount($this->request(15, 'daily'));

        $this->assertSame(1, $controller->queries['bills_paid_amount']);
        $this->assertSame($first['daily']['datasets'][0]['data'], $second['daily']['datasets'][0]['data']);
        $this->assertSame('5.50', $second['daily']['datasets'][0]['data'][0]);
        $this->assertSame(2, $second['daily']['datasets'][0]['data'][2]);
        $this->assertSame(0, $second['daily']['datasets'][0]['data'][1]);
        $cached = Cache::get($this->key(15, 'bills_paid_amount', 'daily', '2026-10-04', '2026-10-10'));
        $this->assertSame(['daily' => [1 => '5.50', 3 => 2]], $cached);
        $this->assertIsArray($cached['daily']);
    }

    public function test_cached_response_matches_uncached_datasets(): void
    {
        $controller = $this->harness();
        $controller->today = $this->day('2026-10-06');
        $controller->results['bills_count'] = ['monthly' => [10 => 7, 1 => 1]];

        config([
            'surebills_cache.enabled' => false,
            'surebills_cache.domains.merchant_charts' => false,
        ]);
        $uncached = $controller->billsCount($this->request(21, 'monthly'));

        $this->enableCharts();
        Cache::flush();
        $controller->queries['bills_count'] = 0;
        $cached = $controller->billsCount($this->request(21, 'monthly'));

        $this->assertSame(
            json_decode(json_encode($uncached), true),
            json_decode(json_encode($cached), true)
        );
        $this->assertSame(1, $controller->queries['bills_count']);
        $this->assertCount(12, $cached['monthly']['datasets'][0]['data']);
        $this->assertArrayHasKey('daily', $cached);
        $this->assertArrayHasKey('weekly', $cached);
        $this->assertArrayHasKey('monthly', $cached);
    }

    public function test_metrics_modes_and_merchants_do_not_share_keys(): void
    {
        $this->enableCharts();
        $controller = $this->harness();
        $controller->today = $this->day('2026-10-06');
        $controller->results = [
            'bills_paid_amount' => ['daily' => [1 => 1], 'weekly' => [40 => 2], 'monthly' => [10 => 3]],
            'bills_paid_count' => ['daily' => [1 => 4]],
            'bills_count' => ['daily' => [1 => 5]],
        ];

        $controller->billsPaidAmount($this->request(15, 'daily'));
        $controller->billsPaidAmount($this->request(15, 'weekly'));
        $controller->billsPaidAmount($this->request(15, 'monthly'));
        $controller->billsPaidCount($this->request(15, 'daily'));
        $controller->billsCount($this->request(16, 'daily'));

        $this->assertSame([1 => 1], Cache::get($this->key(15, 'bills_paid_amount', 'daily', '2026-10-04', '2026-10-10'))['daily']);
        $this->assertSame([40 => 2], Cache::get($this->key(15, 'bills_paid_amount', 'weekly', '2026-10-01', '2026-10-31'))['weekly']);
        $this->assertSame([10 => 3], Cache::get($this->key(15, 'bills_paid_amount', 'monthly', '2026-01-01', '2026-12-31'))['monthly']);
        $this->assertSame([1 => 4], Cache::get($this->key(15, 'bills_paid_count', 'daily', '2026-10-04', '2026-10-10'))['daily']);
        $this->assertSame([1 => 5], Cache::get($this->key(16, 'bills_count', 'daily', '2026-10-04', '2026-10-10'))['daily']);
        $this->assertNull(Cache::get($this->key(15, 'bills_count', 'daily', '2026-10-04', '2026-10-10')));
    }

    public function test_daily_remember_uses_mysql_week_bounds_and_week_ttl(): void
    {
        $this->assertSame(604800, config('surebills_cache.ttl.charts_week'));
        $this->assertSame('41', $this->day('2026-10-06')->format('W'));

        $this->assertRemembered(
            'billsPaidAmount',
            'bills_paid_amount',
            15,
            'daily',
            '2026-10-06',
            'merchant:15:charts:bills_paid_amount:daily:2026-10-04:2026-10-10',
            604800
        );
        $this->assertRemembered(
            'billsPaidAmount',
            'bills_paid_amount',
            15,
            'daily',
            '2026-10-10',
            'merchant:15:charts:bills_paid_amount:daily:2026-10-04:2026-10-10',
            604800
        );
        $this->assertRemembered(
            'billsPaidAmount',
            'bills_paid_amount',
            15,
            'daily',
            '2026-10-11',
            'merchant:15:charts:bills_paid_amount:daily:2026-10-11:2026-10-17',
            604800
        );
    }

    public function test_weekly_remember_uses_calendar_month_and_month_ttl(): void
    {
        $this->assertSame(2592000, config('surebills_cache.ttl.charts_month'));

        $this->assertRemembered(
            'billsPaidCount',
            'bills_paid_count',
            15,
            'weekly',
            '2026-10-06',
            'merchant:15:charts:bills_paid_count:weekly:2026-10-01:2026-10-31',
            2592000
        );
    }

    public function test_monthly_remember_uses_calendar_year_and_year_ttl(): void
    {
        $this->assertSame(86400, config('surebills_cache.ttl.charts_year'));

        $this->assertRemembered(
            'billsCount',
            'bills_count',
            15,
            'monthly',
            '2026-10-06',
            'merchant:15:charts:bills_count:monthly:2026-01-01:2026-12-31',
            86400
        );
    }

    public function test_daily_window_changes_with_mysql_week_not_iso_week(): void
    {
        $controller = $this->harness();

        $this->assertSame(['2026-10-04', '2026-10-10'], $controller->exposedWindow('daily', $this->day('2026-10-06')));
        $this->assertSame(['2026-10-04', '2026-10-10'], $controller->exposedWindow('daily', $this->day('2026-10-10')));
        $this->assertSame(['2026-10-11', '2026-10-17'], $controller->exposedWindow('daily', $this->day('2026-10-11')));
        $this->assertSame(['2026-01-01', '2026-01-03'], $controller->exposedWindow('daily', $this->day('2026-01-01')));
        $this->assertSame(['2026-01-04', '2026-01-10'], $controller->exposedWindow('daily', $this->day('2026-01-04')));
        $this->assertSame(['2026-10-01', '2026-10-31'], $controller->exposedWindow('weekly', $this->day('2026-10-06')));
        $this->assertSame(['2026-11-01', '2026-11-30'], $controller->exposedWindow('weekly', $this->day('2026-11-01')));
        $this->assertSame(['2026-01-01', '2026-12-31'], $controller->exposedWindow('monthly', $this->day('2026-10-06')));
        $this->assertSame(['2027-01-01', '2027-12-31'], $controller->exposedWindow('monthly', $this->day('2027-01-01')));
    }

    public function test_empty_aggregate_keeps_zero_filled_series(): void
    {
        $this->enableCharts();
        $controller = $this->harness();
        $controller->today = $this->day('2026-10-06');
        $controller->results['bills_paid_amount'] = ['daily' => []];

        $response = $controller->billsPaidAmount($this->request(15, 'daily'));

        $this->assertSame([0, 0, 0, 0, 0, 0, 0], $response['daily']['datasets'][0]['data']);
        $this->assertCount(12, $response['monthly']['datasets'][0]['data']);
    }

    public function test_unknown_mode_does_not_create_a_cache_entry(): void
    {
        $this->enableCharts();
        Cache::spy();
        $controller = $this->harness();
        $controller->results['bills_paid_amount'] = ['daily' => [1 => 8]];

        $response = $controller->billsPaidAmount($this->request(15, 'yearly'));

        Cache::shouldNotHaveReceived('remember');
        $this->assertSame(1, $controller->queries['bills_paid_amount']);
        $this->assertSame([0, 0, 0, 0, 0, 0, 0], $response['daily']['datasets'][0]['data']);
    }

    public function test_language_switch_rebuilds_labels_from_the_same_aggregate(): void
    {
        $this->enableCharts();
        $controller = $this->harness();
        $controller->today = $this->day('2026-10-06');
        $controller->results['bills_paid_amount'] = ['daily' => [1 => 6]];

        app()->setLocale('en');
        $english = $controller->billsPaidAmount($this->request(15, 'daily'));
        app()->setLocale('ar');
        $arabic = $controller->billsPaidAmount($this->request(15, 'daily'));

        $this->assertSame(1, $controller->queries['bills_paid_amount']);
        $this->assertNotSame($english['daily']['labels'][0], $arabic['daily']['labels'][0]);
        $this->assertSame($english['daily']['datasets'][0]['data'], $arabic['daily']['datasets'][0]['data']);
        $this->assertSame('Sunday', $english['daily']['labels'][0]);
    }

    public function test_redis_failure_still_returns_the_aggregate(): void
    {
        $this->enableCharts();

        Cache::shouldReceive('remember')
            ->once()
            ->andThrow(new RuntimeException('redis down'));

        $controller = $this->harness();
        $controller->today = $this->day('2026-10-06');
        $controller->results['bills_paid_amount'] = ['daily' => [1 => 4]];

        $response = $controller->billsPaidAmount($this->request(15, 'daily'));

        $this->assertSame(1, $controller->queries['bills_paid_amount']);
        $this->assertSame(4, $response['daily']['datasets'][0]['data'][0]);
    }

    private function assertRemembered(
        string $method,
        string $metric,
        int $userId,
        string $mode,
        string $day,
        string $key,
        int $ttl
    ): void {
        $cache = Mockery::mock(SureBillsCache::class);
        $cache->shouldReceive('enabled')->once()->with('merchant_charts')->andReturn(true);
        $cache->shouldReceive('remember')->once()->with(
            $key,
            $ttl,
            Mockery::type('Closure'),
            'merchant_charts'
        )->andReturnUsing(function ($key, $ttl, $callback) {
            return $callback();
        });

        $controller = $this->harness($cache);
        $controller->today = $this->day($day);
        $controller->results[$metric] = [$mode => [1 => 1]];
        $controller->{$method}($this->request($userId, $mode));
    }

    private function enableCharts(): void
    {
        config([
            'surebills_cache.enabled' => true,
            'surebills_cache.fail_open' => true,
            'surebills_cache.log_sample_rate' => 0,
            'surebills_cache.domains.merchant_charts' => true,
        ]);
    }

    /**
     * @param  SureBillsCache|Mockery\MockInterface  $cache
     */
    private function harness($cache = null): ChartsCacheHarness
    {
        return new ChartsCacheHarness($cache ?: $this->app->make(SureBillsCache::class));
    }

    private function request(int $userId, string $mode): Request
    {
        return Request::create('/api/v1/charts/bills_paid_amount', 'GET', [
            'user_id' => $userId,
            'mode' => $mode,
        ]);
    }

    private function day(string $day): DateTimeImmutable
    {
        return new DateTimeImmutable($day, new DateTimeZone('Asia/Riyadh'));
    }

    private function key(int $userId, string $metric, string $mode, string $from, string $to): string
    {
        return CacheKeys::charts($userId, $metric, $mode, $from, $to);
    }
}

class ChartsCacheHarness extends ChartsController
{
    /** @var array<string, int> */
    public $queries = [
        'bills_paid_amount' => 0,
        'bills_paid_count' => 0,
        'bills_count' => 0,
    ];

    /** @var array<string, array> */
    public $results = [
        'bills_paid_amount' => [],
        'bills_paid_count' => [],
        'bills_count' => [],
    ];

    /** @var DateTimeImmutable|null */
    public $today;

    public function exposedWindow(string $mode, DateTimeImmutable $day): array
    {
        return $this->chartWindow($mode, $day);
    }

    protected function chartToday(): DateTimeImmutable
    {
        return $this->today ?: parent::chartToday();
    }

    protected function chartUser($userId)
    {
        return new ChartsCacheUser((int) $userId);
    }

    protected function queryBillsPaidAmount($user, $mode)
    {
        return $this->aggregate('bills_paid_amount', $mode);
    }

    protected function queryBillsPaidCount($user, $mode)
    {
        return $this->aggregate('bills_paid_count', $mode);
    }

    protected function queryBillsCount($user, $mode)
    {
        return $this->aggregate('bills_count', $mode);
    }

    /**
     * @param  mixed  $mode
     * @return array
     */
    private function aggregate(string $metric, $mode)
    {
        $this->queries[$metric]++;

        if (! in_array($mode, ['daily', 'weekly', 'monthly'], true)) {
            return [];
        }

        return $this->results[$metric];
    }
}

class ChartsCacheUser
{
    /** @var int */
    public $id;

    /** @var int|null */
    public $store_main_user_id = null;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}
