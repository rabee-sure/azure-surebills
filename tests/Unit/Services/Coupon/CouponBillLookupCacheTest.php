<?php

namespace Tests\Unit\Services\Coupon;

use App\Models\Coupon;
use App\Models\CouponCode;
use App\Models\CouponUsage;
use App\Models\User;
use App\Repositories\CouponRepository;
use App\Rules\BillTotalValidation;
use App\Services\Cache\SureBillsCache;
use App\Services\Coupon\CouponService;
use App\Services\Coupon\RedemptionClaim;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

class CouponBillLookupCacheTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private const USER_ID = 15;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-10-04 15:00:00', 'Asia/Riyadh'));
        config(['surebills_cache.ttl.coupons' => 86400]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_flag_off_uses_find_by_code_and_does_not_touch_redis(): void
    {
        $coupon = $this->coupon(10, 'SAVE10', '10.00');
        $repository = $this->repository();
        $cache = $this->cache();
        $cache->shouldReceive('enabled')->once()->with('coupons')->andReturn(false);
        $cache->shouldReceive('remember')->never();
        $cache->shouldReceive('forget')->never();
        $repository->shouldReceive('findByCode')->once()->with('SAVE10', self::USER_ID)->andReturn($coupon);
        $repository->shouldReceive('reusableAvailableToday')->never();
        $repository->shouldReceive('findReusableById')->never();

        $found = $this->service($cache, $repository)->findReusableForBillLookup('SAVE10', self::USER_ID);

        $this->assertSame($coupon, $found);
    }

    public function test_flag_on_cache_miss_uses_the_merchant_day_key_and_loads_the_list(): void
    {
        $coupon = $this->coupon(10, 'SAVE10', '10.00');
        $repository = $this->repository();
        $cache = $this->cache();
        $seen = null;

        $cache->shouldReceive('enabled')->once()->with('coupons')->andReturn(true);
        $cache->shouldReceive('forget')->never();
        $repository->shouldReceive('reusableAvailableToday')->once()->with(self::USER_ID)->andReturn([
            ['id' => 10, 'code_pattern' => 'SAVE10'],
        ]);
        $repository->shouldReceive('findReusableById')->once()->with(10, 'SAVE10', self::USER_ID)->andReturn($coupon);
        $repository->shouldReceive('findByCode')->never();
        $cache->shouldReceive('remember')->once()->andReturnUsing(
            function ($key, $ttl, $callback, $domain) use (&$seen) {
                $seen = ['key' => $key, 'ttl' => $ttl, 'domain' => $domain];

                return $callback();
            }
        );

        $found = $this->service($cache, $repository)->findReusableForBillLookup('SAVE10', self::USER_ID);

        $this->assertSame($coupon, $found);
        $this->assertSame($this->todayKey(), $seen['key']);
        $this->assertSame(config('surebills_cache.ttl.coupons'), $seen['ttl']);
        $this->assertSame(86400, $seen['ttl']);
        $this->assertSame('coupons', $seen['domain']);
    }

    public function test_cache_hit_reloads_the_mysql_coupon_and_ignores_cached_discount_fields(): void
    {
        $coupon = $this->coupon(10, 'SAVE10', '25.50');
        $repository = $this->repository();
        $cache = $this->cache();

        $cache->shouldReceive('enabled')->once()->with('coupons')->andReturn(true);
        $cache->shouldReceive('remember')->once()->andReturn([
            ['id' => 10, 'code_pattern' => 'SAVE10'],
        ]);
        $repository->shouldReceive('reusableAvailableToday')->never();
        $repository->shouldReceive('findReusableById')->once()->with(10, 'save10', self::USER_ID)->andReturn($coupon);
        $repository->shouldReceive('findByCode')->never();

        $found = $this->service($cache, $repository)->findReusableForBillLookup('save10', self::USER_ID);

        $this->assertInstanceOf(Coupon::class, $found);
        $this->assertSame($coupon, $found);
        $this->assertSame('25.50', (string) $found->discount_value);
    }

    public function test_stale_cached_candidate_falls_back_to_find_by_code(): void
    {
        $coupon = $this->coupon(11, 'SAVE10', '4.00');
        $repository = $this->repository();
        $cache = $this->cache();

        $cache->shouldReceive('enabled')->andReturn(true);
        $cache->shouldReceive('remember')->once()->andReturn([
            ['id' => 10, 'code_pattern' => 'SAVE10'],
        ]);
        $repository->shouldReceive('findReusableById')->once()->with(10, 'SAVE10', self::USER_ID)->andReturn(null);
        $repository->shouldReceive('findByCode')->once()->with('SAVE10', self::USER_ID)->andReturn($coupon);

        $found = $this->service($cache, $repository)->findReusableForBillLookup('SAVE10', self::USER_ID);

        $this->assertSame($coupon, $found);
    }

    public function test_unknown_reusable_code_falls_back_to_find_by_code(): void
    {
        $coupon = $this->coupon(11, 'OLDCODE', '4.00');
        $repository = $this->repository();
        $cache = $this->cache();

        $cache->shouldReceive('enabled')->andReturn(true);
        $cache->shouldReceive('remember')->once()->andReturn([
            ['id' => 10, 'code_pattern' => 'SAVE10'],
        ]);
        $repository->shouldReceive('findReusableById')->never();
        $repository->shouldReceive('findByCode')->once()->with('OLDCODE', self::USER_ID)->andReturn($coupon);

        $found = $this->service($cache, $repository)->findReusableForBillLookup('OLDCODE', self::USER_ID);

        $this->assertSame($coupon, $found);
    }

    public function test_create_update_toggle_delete_and_reusable_usage_forget_todays_key(): void
    {
        $coupon = $this->coupon(10, 'SAVE10', '10.00');
        $coupon->mechanism = 'max_usage';
        $repository = $this->repository();
        $cache = $this->cache();
        $cache->shouldReceive('enabled')->andReturn(true);
        $cache->shouldReceive('forget')->times(6)->with($this->todayKey())->andReturn(true);
        $repository->shouldReceive('create')->once()->andReturn($coupon);
        $repository->shouldReceive('update')->twice()->andReturn(true);
        $repository->shouldReceive('delete')->twice()->andReturn(true);
        $repository->shouldReceive('getUsageCount')->once()->with(10)->andReturn(0);
        $repository->shouldReceive('createUsage')->once()->andReturn($this->usage());

        $service = $this->service($cache, $repository);
        $service->createCoupon([
            'mechanism' => 'max_usage',
            'code_pattern' => 'SAVE10',
        ], self::USER_ID);
        $service->updateCoupon($coupon, ['name' => 'Renamed']);
        $service->toggleStatus($coupon);
        $service->deleteCoupon($coupon);
        $service->deleteCouponIfUnused($coupon);

        DB::shouldReceive('transactionLevel')->once()->andReturn(1);
        $claim = new RedemptionClaim('SAVE10', self::USER_ID, $coupon, null);
        $this->activateClaim($service, $claim);
        $service->recordUsage($claim, 'bill-1');
    }

    public function test_failed_toggle_one_time_usage_preview_and_bulk_generation_do_not_forget(): void
    {
        $reusable = $this->coupon(10, 'SAVE10', '10.00');
        $reusable->valid_from = '2026-10-05 00:00:00';
        $oneTime = $this->coupon(12, '{RANDOM:8}', '10.00');
        $oneTime->mechanism = 'one_time_usage';
        $oneTimeCode = new CouponCode();
        $oneTimeCode->id = 44;

        $repository = $this->repository();
        $cache = $this->cache();
        $cache->shouldReceive('enabled')->andReturn(true);
        $cache->shouldReceive('remember')->never();
        $cache->shouldReceive('forget')->never();
        $repository->shouldReceive('update')->never();
        $repository->shouldReceive('findByCode')->once()->with('MISSING', self::USER_ID)->andReturn(null);
        $repository->shouldReceive('findCodeByCode')->once()->with('MISSING', self::USER_ID)->andReturn(null);
        $repository->shouldReceive('getCodesForCoupon')->once()->with(12)->andReturn(new Collection());
        $repository->shouldReceive('bulkCreateCodes')->once()->andReturn([]);
        $repository->shouldReceive('createUsage')->once()->andReturn($this->usage());
        $repository->shouldReceive('markCodeAsUsedIfAvailable')->once()->with(44)->andReturn(true);

        $service = $this->service($cache, $repository);
        $refused = $service->toggleStatus($reusable);
        $preview = $service->validateCoupon('MISSING', 9, self::USER_ID);
        $generated = $service->generateBulkCodes($oneTime, 1);

        DB::shouldReceive('transactionLevel')->once()->andReturn(1);
        $claim = new RedemptionClaim('ONCE', self::USER_ID, $oneTime, $oneTimeCode);
        $this->activateClaim($service, $claim);
        $service->recordUsage($claim, 'bill-1');

        $this->assertFalse($refused['success']);
        $this->assertFalse($preview['success']);
        $this->assertTrue($generated['success']);
    }

    public function test_bill_total_validation_uses_the_reusable_lookup_and_keeps_one_time_fallback(): void
    {
        $this->actAsMerchant();

        $reusable = Mockery::mock(CouponService::class);
        $reusableRepository = Mockery::mock(CouponRepository::class);
        $exhausted = $this->coupon(10, 'SAVE10', '10000.00');
        $reusable->shouldReceive('findReusableForBillLookup')->once()->with('SAVE10', self::USER_ID)->andReturn($exhausted);
        $reusableRepository->shouldReceive('findCodeByCode')->never();

        $reusableRule = new BillTotalValidation($reusable, $reusableRepository);
        $this->bindBillRequest('SAVE10');

        $this->assertFalse($reusableRule->passes('items', request()->items));

        $oneTime = Mockery::mock(CouponService::class);
        $oneTimeRepository = Mockery::mock(CouponRepository::class);
        $code = new CouponCode();
        $code->setRelation('coupon', $this->coupon(12, 'ONCE', '10000.00'));
        $oneTime->shouldReceive('findReusableForBillLookup')->once()->with('ONCE', self::USER_ID)->andReturn(null);
        $oneTimeRepository->shouldReceive('findCodeByCode')->once()->with('ONCE', self::USER_ID)->andReturn($code);

        $oneTimeRule = new BillTotalValidation($oneTime, $oneTimeRepository);
        $this->bindBillRequest('ONCE');

        $this->assertFalse($oneTimeRule->passes('items', request()->items));
    }

    private function service(SureBillsCache $cache, CouponRepository $repository): CouponService
    {
        return new CouponService($cache, $repository);
    }

    private function cache()
    {
        return Mockery::mock(SureBillsCache::class);
    }

    private function repository()
    {
        return Mockery::mock(CouponRepository::class);
    }

    private function coupon(int $id, string $code, string $discount): Coupon
    {
        $coupon = new Coupon();
        $coupon->setDateFormat('Y-m-d H:i:s');
        $coupon->id = $id;
        $coupon->user_id = self::USER_ID;
        $coupon->code_pattern = $code;
        $coupon->discount_type = 'fixed';
        $coupon->discount_value = $discount;
        $coupon->is_active = true;

        return $coupon;
    }

    private function usage(): CouponUsage
    {
        $usage = new CouponUsage();
        $usage->id = 5;

        return $usage;
    }

    private function activateClaim(CouponService $service, RedemptionClaim $claim): void
    {
        $claim->markAsserted(9);
        $property = new \ReflectionProperty(CouponService::class, 'activeClaim');
        $property->setAccessible(true);
        $property->setValue($service, $claim);
    }

    private function todayKey(): string
    {
        return 'merchant:'.self::USER_ID.':coupons:available:20261004';
    }

    private function actAsMerchant(): void
    {
        $user = new User();
        $user->id = self::USER_ID;
        $user->store_main_user_id = self::USER_ID;
        $this->actingAs($user);
    }

    private function bindBillRequest(string $code): void
    {
        $this->app->instance('request', Request::create('/bills', 'POST', [
            'coupon_code' => $code,
            'items' => [
                ['name' => 'Item', 'price' => 100, 'quantity' => 1],
            ],
        ]));
    }
}
