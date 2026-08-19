<?php

namespace Tests\Unit\Services;

use App\Models\Application;
use App\Services\ApplicationFeeConfigService;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class ApplicationFeeConfigServiceCacheTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private $cache;
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cache = Mockery::mock(SureBillsCache::class);
        $this->service = Mockery::mock(ApplicationFeeConfigService::class, [$this->cache])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    private function application(?int $channelId = 12): Application
    {
        return (new Application())->forceFill([
            'id' => 21,
            'user_id' => 5,
            'channel_id' => $channelId,
            'name' => 'Merchant App',
            'mada_fixed' => '0.12345',
            'mada_percentage' => '1.25000',
            'credit_cards_fixed' => '2.50000',
            'credit_cards_percentage' => '3.75000',
            'secret' => 'excluded',
            'webhook_secret' => 'excluded',
        ]);
    }

    public function test_flag_off_reads_database_only(): void
    {
        $application = $this->application();
        $this->cache->shouldReceive('enabled')->once()->with('channels')->andReturn(false);
        $this->cache->shouldReceive('rememberForever')->never();
        $this->service->shouldReceive('loadFromDatabase')->once()->with(21)->andReturn($application);

        $this->assertSame($application, $this->service->get(21));
    }

    public function test_cold_cache_uses_application_key(): void
    {
        $application = $this->application();
        $this->cache->shouldReceive('enabled')->once()->with('channels')->andReturn(true);
        $this->service->shouldReceive('loadFromDatabase')->once()->with(21)->andReturn($application);
        $this->cache->shouldReceive('rememberForever')
            ->once()
            ->with(CacheKeys::application(21), Mockery::type('Closure'), 'channels')
            ->andReturnUsing(fn ($key, $callback) => $callback());
        $this->service->shouldReceive('hydrate')
            ->once()
            ->with(ApplicationFeeConfigService::toPayload($application))
            ->andReturn($application);

        $this->assertSame('0.12345', $this->service->get(21)->mada_fixed);
    }

    public function test_warm_cache_skips_database(): void
    {
        $this->cache->shouldReceive('enabled')->once()->with('channels')->andReturn(true);
        $this->cache->shouldReceive('rememberForever')->once()->andReturn(
            ApplicationFeeConfigService::toPayload($this->application())
        );
        $this->service->shouldReceive('loadFromDatabase')->never();
        $this->service->shouldReceive('hydrate')->once()->andReturn($this->application());

        $this->assertSame('3.75000', $this->service->get(21)->credit_cards_percentage);
    }

    public function test_fail_open_callback_returns_database_configuration(): void
    {
        $application = $this->application();
        $this->cache->shouldReceive('enabled')->once()->with('channels')->andReturn(true);
        $this->cache->shouldReceive('rememberForever')
            ->once()
            ->andReturnUsing(fn ($key, $callback) => $callback());
        $this->service->shouldReceive('loadFromDatabase')->once()->with(21)->andReturn($application);
        $this->service->shouldReceive('hydrate')->once()->andReturn($application);

        $this->assertSame($application, $this->service->get(21));
    }

    public function test_nullable_channel_id_round_trips(): void
    {
        $payload = ApplicationFeeConfigService::toPayload($this->application(null));
        $this->assertNull($payload['channel_id']);
    }

    public function test_payload_excludes_credentials_and_financial_results(): void
    {
        $payload = ApplicationFeeConfigService::toPayload($this->application());
        $this->assertArrayNotHasKey('secret', $payload);
        $this->assertArrayNotHasKey('webhook_secret', $payload);
        $this->assertArrayNotHasKey('payment_fees', $payload);
        $this->assertSame('1.25000', $payload['mada_percentage']);
    }

    public function test_incomplete_cached_payload_falls_back_to_database(): void
    {
        $application = $this->application();
        $this->cache->shouldReceive('enabled')->once()->with('channels')->andReturn(true);
        $this->cache->shouldReceive('rememberForever')->once()->andReturn(['id' => 21]);
        $this->service->shouldReceive('hydrate')->never();
        $this->service->shouldReceive('loadFromDatabase')->once()->with(21)->andReturn($application);

        $this->assertSame($application, $this->service->get(21));
    }

    public function test_wrong_cached_entity_falls_back_to_requested_database_entity(): void
    {
        $application = $this->application();
        $wrong = $this->application();
        $wrong->id = 22;
        $this->cache->shouldReceive('enabled')->once()->with('channels')->andReturn(true);
        $this->cache->shouldReceive('rememberForever')
            ->once()
            ->andReturn(ApplicationFeeConfigService::toPayload($wrong));
        $this->service->shouldReceive('hydrate')->never();
        $this->service->shouldReceive('loadFromDatabase')->once()->with(21)->andReturn($application);

        $this->assertSame(21, $this->service->get(21)->id);
    }

    public function test_put_and_forget_use_shared_contract(): void
    {
        $application = $this->application();
        $this->cache->shouldReceive('enabled')->twice()->with('channels')->andReturn(true);
        $this->cache->shouldReceive('put')
            ->once()
            ->with(CacheKeys::application(21), Mockery::type('array'))
            ->andReturn(true);
        $this->cache->shouldReceive('forget')->once()->with(CacheKeys::application(21))->andReturn(true);

        $this->assertTrue($this->service->put($application));
        $this->assertTrue($this->service->forget(21));
    }
}
