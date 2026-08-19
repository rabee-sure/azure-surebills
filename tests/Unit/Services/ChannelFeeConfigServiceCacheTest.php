<?php

namespace Tests\Unit\Services;

use App\Models\Channel;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use App\Services\ChannelFeeConfigService;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class ChannelFeeConfigServiceCacheTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private $cache;
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cache = Mockery::mock(SureBillsCache::class);
        $this->service = Mockery::mock(ChannelFeeConfigService::class, [$this->cache])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    private function channel(): Channel
    {
        return (new Channel())->forceFill([
            'id' => 11,
            'user_id' => 5,
            'name' => 'Shared Channel',
            'mada_fixed' => '0.12345',
            'mada_percentage' => '1.25000',
            'credit_cards_fixed' => '2.50000',
            'credit_cards_percentage' => '3.75000',
            'secret_token' => 'excluded',
        ]);
    }

    public function test_flag_off_reads_database_without_cache(): void
    {
        $channel = $this->channel();
        $this->cache->shouldReceive('enabled')->once()->with('channels')->andReturn(false);
        $this->cache->shouldReceive('rememberForever')->never();
        $this->service->shouldReceive('loadFromDatabase')->once()->with(11)->andReturn($channel);

        $this->assertSame($channel, $this->service->get(11));
    }

    public function test_cold_cache_uses_shared_key_and_database_callback(): void
    {
        $channel = $this->channel();
        $this->cache->shouldReceive('enabled')->once()->with('channels')->andReturn(true);
        $this->service->shouldReceive('loadFromDatabase')->once()->with(11)->andReturn($channel);
        $this->cache->shouldReceive('rememberForever')
            ->once()
            ->with(CacheKeys::channel(11), Mockery::type('Closure'), 'channels')
            ->andReturnUsing(fn ($key, $callback) => $callback());
        $this->service->shouldReceive('hydrate')
            ->once()
            ->with(ChannelFeeConfigService::toPayload($channel))
            ->andReturn($channel);

        $this->assertSame('0.12345', $this->service->get(11)->mada_fixed);
    }

    public function test_warm_cache_skips_database_and_preserves_rates(): void
    {
        $this->cache->shouldReceive('enabled')->once()->with('channels')->andReturn(true);
        $this->cache->shouldReceive('rememberForever')->once()->andReturn(
            ChannelFeeConfigService::toPayload($this->channel())
        );
        $this->service->shouldReceive('loadFromDatabase')->never();
        $this->service->shouldReceive('hydrate')->once()->andReturn($this->channel());

        $channel = $this->service->get(11);
        $this->assertSame('1.25000', $channel->mada_percentage);
        $this->assertSame('3.75000', $channel->credit_cards_percentage);
    }

    public function test_fail_open_callback_returns_database_configuration(): void
    {
        $channel = $this->channel();
        $this->cache->shouldReceive('enabled')->once()->with('channels')->andReturn(true);
        $this->cache->shouldReceive('rememberForever')
            ->once()
            ->andReturnUsing(fn ($key, $callback) => $callback());
        $this->service->shouldReceive('loadFromDatabase')->once()->with(11)->andReturn($channel);
        $this->service->shouldReceive('hydrate')->once()->andReturn($channel);

        $this->assertSame($channel, $this->service->get(11));
    }

    public function test_incomplete_cached_payload_falls_back_to_database(): void
    {
        $channel = $this->channel();
        $this->cache->shouldReceive('enabled')->once()->with('channels')->andReturn(true);
        $this->cache->shouldReceive('rememberForever')->once()->andReturn(['id' => 11]);
        $this->service->shouldReceive('hydrate')->never();
        $this->service->shouldReceive('loadFromDatabase')->once()->with(11)->andReturn($channel);

        $this->assertSame($channel, $this->service->get(11));
    }

    public function test_wrong_cached_entity_falls_back_to_requested_database_entity(): void
    {
        $channel = $this->channel();
        $wrong = $this->channel();
        $wrong->id = 12;
        $this->cache->shouldReceive('enabled')->once()->with('channels')->andReturn(true);
        $this->cache->shouldReceive('rememberForever')
            ->once()
            ->andReturn(ChannelFeeConfigService::toPayload($wrong));
        $this->service->shouldReceive('hydrate')->never();
        $this->service->shouldReceive('loadFromDatabase')->once()->with(11)->andReturn($channel);

        $this->assertSame(11, $this->service->get(11)->id);
    }

    public function test_payload_is_fee_config_only(): void
    {
        $payload = ChannelFeeConfigService::toPayload($this->channel());
        $this->assertArrayNotHasKey('secret_token', $payload);
        $this->assertArrayNotHasKey('payment_fees', $payload);
        $this->assertSame('2.50000', $payload['credit_cards_fixed']);
    }
}
