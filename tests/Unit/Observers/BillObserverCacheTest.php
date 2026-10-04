<?php

namespace Tests\Unit\Observers;

use App\Http\Controllers\Api\BillController;
use App\Models\Bill;
use App\Observers\BillObserver;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use App\Services\Coupon\CouponService;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class BillObserverCacheTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private $cache;
    private $store;
    private $logger;
    private BillObserver $observer;
    private ?Container $previousContainer = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = Facade::getFacadeApplication();
        $app = new Container;
        $app->instance('config', new Repository([
            'surebills_cache' => [
                'ttl' => ['pending_bills' => 111],
            ],
        ]));
        Container::setInstance($app);
        Facade::setFacadeApplication($app);
        Facade::clearResolvedInstance('log');
        Facade::clearResolvedInstance('cache');
        $this->logger = Mockery::mock()->shouldIgnoreMissing();
        $app->instance('log', $this->logger);
        $this->store = Mockery::mock();
        $app->instance('cache', $this->store);
        $this->cache = Mockery::mock(SureBillsCache::class);
        $this->observer = new BillObserver($this->cache);
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstance('log');
        Facade::clearResolvedInstance('cache');
        Facade::setFacadeApplication($this->previousContainer);
        Container::setInstance($this->previousContainer);
        parent::tearDown();
    }

    public function test_flag_off_created_pending_does_not_write(): void
    {
        $this->cache->shouldReceive('enabled')->once()->with('pending_bills')->andReturn(false);
        $this->cache->shouldReceive('put')->never();
        $this->cache->shouldReceive('forget')->never();
        $this->store->shouldReceive('get')->never();

        $this->observer->created($this->bill('bill-a', 'pending'));
    }

    public function test_flag_off_pending_to_paid_does_not_write(): void
    {
        $this->cache->shouldReceive('enabled')->once()->with('pending_bills')->andReturn(false);
        $this->cache->shouldReceive('put')->never();
        $this->cache->shouldReceive('forget')->never();
        $this->store->shouldReceive('get')->never();

        $this->observer->updated($this->bill('bill-a', 'paid'));
    }

    public function test_created_pending_bill_is_added_with_the_allow_list(): void
    {
        $stored = null;
        $this->enable();
        $this->store->shouldReceive('get')->once()->with('merchant:4:bills:pending')->andReturn(null);
        $this->cache->shouldReceive('put')->once()->andReturnUsing(function ($key, $map, $ttl) use (&$stored) {
            $stored = ['key' => $key, 'map' => $map, 'ttl' => $ttl];

            return true;
        });

        $this->observer->created($this->bill('bill-a', 'pending', 'QA Buyer', '25.50'));

        $this->assertSame('merchant:4:bills:pending', $stored['key']);
        $this->assertSame(111, $stored['ttl']);
        $this->assertSame([
            'id' => 'bill-a',
            'amount' => '25.50',
            'customer_name' => 'QA Buyer',
            'created_at' => '2026-01-02 03:04:05',
            'status' => 'pending',
        ], $stored['map']['bill-a']);
        $this->assertSame(['id', 'amount', 'customer_name', 'created_at', 'status'], array_keys($stored['map']['bill-a']));
    }

    public function test_created_non_pending_bill_is_not_added(): void
    {
        $this->enable();
        $this->store->shouldReceive('get')->once()->with('merchant:4:bills:pending')->andReturn(null);
        $this->cache->shouldReceive('put')->never();
        $this->cache->shouldReceive('forget')->never();

        $this->observer->created($this->bill('bill-a', 'paid'));
    }

    public function test_pending_to_paid_removes_only_that_bill(): void
    {
        $this->assertRemoved('paid');
    }

    public function test_pending_to_expired_removes_only_that_bill(): void
    {
        $this->assertRemoved('expired');
    }

    public function test_pending_to_canceled_removes_only_that_bill(): void
    {
        $this->assertRemoved('canceled');
    }

    public function test_pending_to_failed_removes_only_that_bill(): void
    {
        $this->assertRemoved('failed');
    }

    public function test_non_pending_to_pending_adds_the_bill(): void
    {
        $stored = null;
        $this->enable();
        $this->store->shouldReceive('get')->once()->andReturn([
            'bill-b' => $this->member('bill-b'),
        ]);
        $this->cache->shouldReceive('put')->once()->andReturnUsing(function ($key, $map, $ttl) use (&$stored) {
            $stored = $map;

            return $key === 'merchant:4:bills:pending' && $ttl === 111;
        });

        $this->observer->updated($this->bill('bill-a', 'pending'));

        $this->assertSame('pending', $stored['bill-a']['status']);
        $this->assertSame('bill-b', $stored['bill-b']['id']);
    }

    public function test_pending_to_pending_refreshes_the_member(): void
    {
        $stored = null;
        $this->enable();
        $this->store->shouldReceive('get')->once()->andReturn([
            'bill-a' => $this->member('bill-a'),
            'bill-b' => $this->member('bill-b'),
        ]);
        $this->cache->shouldReceive('put')->once()->andReturnUsing(function ($key, $map, $ttl) use (&$stored) {
            $stored = $map;

            return $key === CacheKeys::pendingBills(4) && $ttl === 111;
        });

        $this->observer->updated($this->bill('bill-a', 'pending', 'Renamed Buyer', '40.00'));

        $this->assertSame('40.00', $stored['bill-a']['amount']);
        $this->assertSame('Renamed Buyer', $stored['bill-a']['customer_name']);
        $this->assertSame('pending', $stored['bill-a']['status']);
        $this->assertSame('5.00', $stored['bill-b']['amount']);
    }

    public function test_non_pending_to_non_pending_does_not_create_a_member(): void
    {
        $this->enable();
        $this->store->shouldReceive('get')->once()->andReturn([
            'bill-b' => $this->member('bill-b'),
        ]);
        $this->cache->shouldReceive('put')->never();
        $this->cache->shouldReceive('forget')->never();

        $this->observer->updated($this->bill('bill-a', 'refunded'));
    }

    public function test_created_pending_preserves_existing_members(): void
    {
        $stored = null;
        $this->enable();
        $this->store->shouldReceive('get')->once()->andReturn([
            'bill-b' => $this->member('bill-b'),
        ]);
        $this->cache->shouldReceive('put')->once()->andReturnUsing(function ($key, $map, $ttl) use (&$stored) {
            $stored = $map;

            return $key === 'merchant:4:bills:pending' && $ttl === 111;
        });

        $this->observer->created($this->bill('bill-a', 'pending'));

        $this->assertArrayHasKey('bill-a', $stored);
        $this->assertSame('bill-b', $stored['bill-b']['id']);
    }

    public function test_removing_the_last_member_forgets_the_key(): void
    {
        $this->enable();
        $this->store->shouldReceive('get')->once()->andReturn([
            'bill-a' => $this->member('bill-a'),
        ]);
        $this->cache->shouldReceive('put')->never();
        $this->cache->shouldReceive('forget')->once()->with('merchant:4:bills:pending')->andReturn(true);

        $this->observer->updated($this->bill('bill-a', 'paid'));
    }

    public function test_cache_write_uses_the_configured_ttl(): void
    {
        $this->enable();
        $this->store->shouldReceive('get')->once()->andReturn(null);
        $ttl = null;
        $this->cache->shouldReceive('put')->once()->andReturnUsing(function ($key, $map, $seenTtl) use (&$ttl) {
            $ttl = $seenTtl;

            return $key === 'merchant:4:bills:pending' && is_array($map);
        });

        $this->observer->created($this->bill('bill-a', 'pending'));

        $this->assertSame(config('surebills_cache.ttl.pending_bills'), $ttl);
        $this->assertSame(111, $ttl);
    }

    public function test_cache_exception_does_not_escape_and_omits_bill_values(): void
    {
        $this->enable();
        $this->store->shouldReceive('get')->once()->andThrow(new RuntimeException('redis down'));
        $this->logger->shouldReceive('warning')->once()->with(
            'pending bills cache sync failed after bill mutation',
            Mockery::on(function ($context) {
                $encoded = json_encode($context);

                return ($context['domain'] ?? null) === 'pending_bills'
                    && ($context['key'] ?? null) === 'merchant:4:bills:pending'
                    && ($context['operation'] ?? null) === 'get'
                    && ($context['exception'] ?? null) === RuntimeException::class
                    && ! str_contains((string) $encoded, 'QA Buyer')
                    && ! str_contains((string) $encoded, '25.50');
            })
        );

        $this->observer->created($this->bill('bill-a', 'pending', 'QA Buyer', '25.50'));
    }

    public function test_store_query_cancel_forgets_that_merchants_pending_key(): void
    {
        $this->assertQueryCancelForgets(15);
    }

    public function test_store_debit_note_query_cancel_forgets_that_merchants_pending_key(): void
    {
        $this->assertQueryCancelForgets(16);
    }

    public function test_wordpress_query_cancel_forgets_that_merchants_pending_key(): void
    {
        $this->assertQueryCancelForgets(17);
    }

    public function test_query_cancel_with_flag_off_does_not_forget(): void
    {
        $cache = Mockery::mock(SureBillsCache::class);
        $cache->shouldReceive('enabled')->once()->with('pending_bills')->andReturn(false);
        $cache->shouldReceive('forget')->never();

        $this->apiController($cache)->expose(15);
    }

    public function test_query_cancel_cache_failure_does_not_escape(): void
    {
        $cache = Mockery::mock(SureBillsCache::class);
        $cache->shouldReceive('enabled')->once()->andReturn(true);
        $cache->shouldReceive('forget')->once()->andThrow(new RuntimeException('redis down'));
        $this->logger->shouldReceive('warning')->once()->with(
            'pending bills cache forget failed after query cancellation',
            Mockery::on(function ($context) {
                return ($context['domain'] ?? null) === 'pending_bills'
                    && ($context['operation'] ?? null) === 'forget'
                    && ($context['exception'] ?? null) === RuntimeException::class
                    && ! array_key_exists('amount', $context)
                    && ! array_key_exists('customer_name', $context);
            })
        );

        $this->apiController($cache)->expose(15);
    }

    public function test_paid_bill_is_not_payable_from_model_state(): void
    {
        $bill = $this->bill('bill-a', 'paid');

        $access = $bill->access_to_pay_page;

        $this->assertFalse($access->status);
        $this->assertSame('paid', $bill->status);
    }

    private function assertRemoved(string $status): void
    {
        $stored = null;
        $this->enable();
        $this->store->shouldReceive('get')->once()->andReturn([
            'bill-a' => $this->member('bill-a'),
            'bill-b' => $this->member('bill-b'),
        ]);
        $this->cache->shouldReceive('put')->once()->with(
            'merchant:4:bills:pending',
            Mockery::on(function ($map) use (&$stored) {
                $stored = $map;

                return ! isset($map['bill-a']) && isset($map['bill-b']);
            }),
            111
        )->andReturn(true);

        $this->observer->updated($this->bill('bill-a', $status));

        $this->assertArrayNotHasKey('bill-a', $stored);
        $this->assertSame('pending', $stored['bill-b']['status']);
    }

    private function assertQueryCancelForgets(int $userId): void
    {
        $cache = Mockery::mock(SureBillsCache::class);
        $cache->shouldReceive('enabled')->once()->with('pending_bills')->andReturn(true);
        $cache->shouldReceive('forget')->once()->with(CacheKeys::pendingBills($userId))->andReturn(true);

        $this->apiController($cache)->expose($userId);
    }

    private function apiController(SureBillsCache $cache): BillController
    {
        return new class(Mockery::mock(CouponService::class), $cache) extends BillController
        {
            public function expose($userId): void
            {
                $this->forgetPendingBillsCache($userId);
            }
        };
    }

    private function enable(): void
    {
        $this->cache->shouldReceive('enabled')->once()->with('pending_bills')->andReturn(true);
    }

    private function bill(string $id, string $status, string $name = 'QA Buyer', string $amount = '10.00', int $userId = 4): Bill
    {
        $bill = new Bill;
        $bill->setDateFormat('Y-m-d H:i:s');
        $bill->setRawAttributes([
            'id' => $id,
            'user_id' => $userId,
            'status' => $status,
            'total' => $amount,
            'customer_name' => $name,
            'created_at' => '2026-01-02 03:04:05',
        ], true);

        return $bill;
    }

    /**
     * @return array<string, mixed>
     */
    private function member(string $id): array
    {
        return [
            'id' => $id,
            'amount' => '5.00',
            'customer_name' => 'Other Buyer',
            'created_at' => '2026-01-01 00:00:00',
            'status' => 'pending',
        ];
    }
}
