<?php

namespace Tests\Unit\Services;

use App\Models\Bank;
use App\Services\BankActiveListService;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

/**
 * PR-08: BankActiveListService shared active-list cache (Merchant read).
 *
 * Pure PHPUnit + Mockery; stubs DB loader and hydrate to avoid Eloquent/Redis.
 * Does NOT use RefreshDatabase, Dusk, or real DB/Redis.
 */
class BankActiveListServiceCacheTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private MockInterface $cache;

    /** @var BankActiveListService&MockInterface */
    private $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cache = Mockery::mock(SureBillsCache::class);
        $this->service = Mockery::mock(BankActiveListService::class, [$this->cache])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    /**
     * @return Bank&MockInterface
     */
    private function mockBank(int $id, string $code, int $sort, array $name): Bank
    {
        $bank = Mockery::mock(Bank::class)->shouldIgnoreMissing();
        $bank->shouldReceive('getAttribute')->with('id')->andReturn($id);
        $bank->shouldReceive('getAttribute')->with('code')->andReturn($code);
        $bank->shouldReceive('getAttribute')->with('sort_number')->andReturn($sort);
        $bank->shouldReceive('offsetExists')->andReturn(true);
        $bank->shouldReceive('getTranslations')->with('name')->andReturn($name);

        return $bank;
    }

    public function test_flag_off_uses_database_loader_only(): void
    {
        $banks = new Collection([$this->mockBank(1, 'A', 1, ['en' => 'A'])]);

        $this->cache->shouldReceive('enabled')->once()->with('banks')->andReturn(false);
        $this->cache->shouldReceive('rememberForever')->never();
        $this->cache->shouldReceive('versionedKey')->never();
        $this->service->shouldReceive('loadActiveBanksFromDatabase')->once()->andReturn($banks);

        $result = $this->service->getActiveBanks();

        $this->assertSame($banks, $result);
    }

    public function test_flag_on_uses_versioned_key_and_remember_forever(): void
    {
        $bank = $this->mockBank(1, 'A', 1, ['en' => 'A']);
        $dbBanks = new Collection([$bank]);
        $hydrated = new Collection([Mockery::mock(Bank::class)]);

        $this->cache->shouldReceive('enabled')->once()->with('banks')->andReturn(true);
        $this->cache->shouldReceive('versionedKey')
            ->once()
            ->with('banks:active', CacheKeys::banksActiveVersion())
            ->andReturn('banks:active:v1');

        $this->service->shouldReceive('loadActiveBanksFromDatabase')->once()->andReturn($dbBanks);

        $this->cache->shouldReceive('rememberForever')
            ->once()
            ->withArgs(function (string $key, $callback, ?string $domain) {
                return $key === 'banks:active:v1'
                    && $domain === 'banks'
                    && is_callable($callback);
            })
            ->andReturnUsing(function (string $key, $callback) {
                return $callback();
            });

        $this->service->shouldReceive('hydrateActiveBanks')
            ->once()
            ->with([
                [
                    'id' => 1,
                    'code' => 'A',
                    'name' => ['en' => 'A'],
                    'sort_number' => 1,
                ],
            ])
            ->andReturn($hydrated);

        $result = $this->service->getActiveBanks();
        $this->assertSame($hydrated, $result);
    }

    public function test_flag_on_warm_skips_database_loader(): void
    {
        $cached = [
            ['id' => 2, 'code' => 'B', 'name' => ['en' => 'B', 'ar' => 'ب'], 'sort_number' => 2],
        ];
        $hydrated = new Collection([Mockery::mock(Bank::class)]);

        $this->cache->shouldReceive('enabled')->once()->with('banks')->andReturn(true);
        $this->cache->shouldReceive('versionedKey')
            ->once()
            ->with('banks:active', CacheKeys::banksActiveVersion())
            ->andReturn('banks:active:v3');
        $this->service->shouldReceive('loadActiveBanksFromDatabase')->never();
        $this->cache->shouldReceive('rememberForever')
            ->once()
            ->with('banks:active:v3', Mockery::type('Closure'), 'banks')
            ->andReturn($cached);
        $this->service->shouldReceive('hydrateActiveBanks')
            ->once()
            ->with($cached)
            ->andReturn($hydrated);

        $result = $this->service->getActiveBanks();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame($hydrated, $result);
    }

    public function test_active_list_payload_excludes_fees_and_relations(): void
    {
        $active = $this->mockBank(1, 'X', 5, ['en' => 'X', 'ar' => 'س']);

        $payload = BankActiveListService::toActiveListPayload(new Collection([$active]));

        $this->assertSame([
            [
                'id' => 1,
                'code' => 'X',
                'name' => ['en' => 'X', 'ar' => 'س'],
                'sort_number' => 5,
            ],
        ], $payload);
        $this->assertArrayNotHasKey('fees', $payload[0]);
        $this->assertArrayNotHasKey('active', $payload[0]);
        $this->assertArrayNotHasKey('users', $payload[0]);
        $this->assertArrayNotHasKey('users_count', $payload[0]);
    }

    public function test_active_list_payload_preserves_collection_order(): void
    {
        $a = $this->mockBank(2, 'A', 1, ['en' => 'A']);
        $b = $this->mockBank(1, 'B', 2, ['en' => 'B']);

        $payload = BankActiveListService::toActiveListPayload(new Collection([$a, $b]));

        $this->assertSame(2, $payload[0]['id']);
        $this->assertSame(1, $payload[1]['id']);
    }

    public function test_flag_off_empty_collection_when_loader_returns_empty(): void
    {
        $this->cache->shouldReceive('enabled')->once()->with('banks')->andReturn(false);
        $this->service->shouldReceive('loadActiveBanksFromDatabase')->once()->andReturn(new Collection([]));

        $result = $this->service->getActiveBanks();
        $this->assertCount(0, $result);
    }

    public function test_cold_payload_uses_translation_map_not_localized_string(): void
    {
        $bank = $this->mockBank(9, 'SNB', 3, ['en' => 'Bank EN', 'ar' => 'بنك']);

        $payload = BankActiveListService::toActiveListPayload(new Collection([$bank]));

        $this->assertIsArray($payload[0]['name']);
        $this->assertSame(['en' => 'Bank EN', 'ar' => 'بنك'], $payload[0]['name']);
    }
}
