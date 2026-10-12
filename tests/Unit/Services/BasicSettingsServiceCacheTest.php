<?php

namespace Tests\Unit\Services;

use App\Services\BasicSettingsService;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

/**
 * PR-06: BasicSettingsService shared cache (Merchant read).
 *
 * Pure unit tests with mocked SureBillsCache and stubbed DB loader.
 * Extends PHPUnit\Framework\TestCase (not Laravel Tests\TestCase) so
 * application bootstrap / Redis / MySQL are never touched.
 *
 * Does NOT use RefreshDatabase, DatabaseMigrations, Dusk, or real DB reads.
 */
class BasicSettingsServiceCacheTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private MockInterface $cache;

    /** @var BasicSettingsService&MockInterface */
    private $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cache = Mockery::mock(SureBillsCache::class);
        $this->service = Mockery::mock(BasicSettingsService::class, [$this->cache])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    public function test_flag_off_get_settings_uses_database_and_skips_cache(): void
    {
        $this->cache->shouldReceive('enabled')
            ->once()
            ->with('basic_settings')
            ->andReturn(false);

        $this->cache->shouldReceive('rememberForever')->never();

        $this->service->shouldReceive('loadSettingsFromDatabase')
            ->once()
            ->andReturn([
                'mobile_number' => '050',
                'transfer_minimum' => '100',
            ]);

        $result = $this->service->getSettings();

        $this->assertSame([
            'mobile_number' => '050',
            'transfer_minimum' => '100',
        ], $result);
    }

    public function test_flag_on_cold_get_uses_remember_forever_with_database_callback(): void
    {
        $this->cache->shouldReceive('enabled')
            ->once()
            ->with('basic_settings')
            ->andReturn(true);

        $this->service->shouldReceive('loadSettingsFromDatabase')
            ->once()
            ->andReturn([
                'mobile_number' => '050',
            ]);

        $this->cache->shouldReceive('rememberForever')
            ->once()
            ->withArgs(function (string $key, $callback, ?string $domain) {
                return $key === CacheKeys::basicSettings()
                    && $domain === 'basic_settings'
                    && is_callable($callback);
            })
            ->andReturnUsing(function (string $key, $callback) {
                return $callback();
            });

        $result = $this->service->getSettings();

        $this->assertSame(['mobile_number' => '050'], $result);
    }

    public function test_flag_on_warm_get_returns_cache_without_database_read(): void
    {
        $cached = [
            'mobile_number' => '050',
            'transfer_minimum' => '200',
        ];

        $this->cache->shouldReceive('enabled')
            ->once()
            ->with('basic_settings')
            ->andReturn(true);

        $this->service->shouldReceive('loadSettingsFromDatabase')->never();

        $this->cache->shouldReceive('rememberForever')
            ->once()
            ->with(CacheKeys::basicSettings(), Mockery::type('Closure'), 'basic_settings')
            ->andReturn($cached);

        $result = $this->service->getSettings();

        $this->assertSame($cached, $result);
    }

    public function test_get_settings_result_shape_is_key_value_map(): void
    {
        $this->cache->shouldReceive('enabled')->once()->with('basic_settings')->andReturn(false);
        $this->service->shouldReceive('loadSettingsFromDatabase')->once()->andReturn([
            'sun' => '1',
            'mon' => '0',
        ]);

        $result = $this->service->getSettings();

        $this->assertIsArray($result);
        $this->assertSame(['sun' => '1', 'mon' => '0'], $result);
        $this->assertFalse(array_is_list($result));
    }

    public function test_get_delegates_to_get_settings_with_default(): void
    {
        $this->cache->shouldReceive('enabled')
            ->twice()
            ->with('basic_settings')
            ->andReturn(false);

        $this->service->shouldReceive('loadSettingsFromDatabase')
            ->twice()
            ->andReturn([
                'mobile_number' => '050',
            ]);

        $this->assertSame('050', $this->service->get('mobile_number'));
        $this->assertSame('fallback', $this->service->get('missing_key', 'fallback'));
    }
}
