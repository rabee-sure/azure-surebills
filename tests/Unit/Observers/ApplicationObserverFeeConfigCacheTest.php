<?php

namespace Tests\Unit\Observers;

use App\Models\Application;
use App\Models\Channel;
use App\Observers\ApplicationObserver;
use App\Services\ApplicationFeeConfigService;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ApplicationObserverFeeConfigCacheTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private $service;
    private $logger;
    private ApplicationObserver $observer;
    private $application;
    private ?Container $previousContainer = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = Facade::getFacadeApplication();
        $app = new Container;
        Facade::setFacadeApplication($app);
        Facade::clearResolvedInstance('log');
        $this->logger = Mockery::mock()->shouldIgnoreMissing();
        $app->instance('log', $this->logger);

        $this->service = Mockery::mock(ApplicationFeeConfigService::class);
        $this->observer = new ApplicationObserver($this->service);
        $this->application = Mockery::mock(Application::class)->makePartial();
        $this->application->forceFill(['id' => 31, 'channel_id' => null]);
        $this->application->shouldReceive('has')->with('channel')->andReturn(false);
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstance('log');
        Facade::setFacadeApplication($this->previousContainer);
        parent::tearDown();
    }

    public function test_created_puts_application_after_existing_created_logic(): void
    {
        $this->service->shouldReceive('put')->once()->with($this->application)->andReturn(true);
        $this->observer->created($this->application);
        $this->assertTrue(true);
    }

    public function test_created_preserves_channel_fee_copy_before_cache_put(): void
    {
        $channel = (new Channel())->forceFill([
            'mada_fixed' => '1.00000',
            'mada_percentage' => '2.00000',
            'credit_cards_fixed' => '3.00000',
            'credit_cards_percentage' => '4.00000',
        ]);
        $application = Mockery::mock(Application::class)->makePartial();
        $application->forceFill([
            'id' => 32,
            'channel_id' => 12,
            'mada_fixed' => null,
            'mada_percentage' => null,
            'credit_cards_fixed' => null,
            'credit_cards_percentage' => null,
        ]);
        $application->setRelation('channel', $channel);
        $application->shouldReceive('has')->with('channel')->andReturn(true);
        $application->shouldReceive('save')->once()->andReturn(true);
        $this->service->shouldReceive('put')
            ->once()
            ->with(Mockery::on(fn (Application $value) => $value->mada_fixed === '1.00000'
                && $value->credit_cards_percentage === '4.00000'))
            ->andReturn(true);

        $this->observer->created($application);
        $this->assertTrue(true);
    }

    public function test_updated_puts_application(): void
    {
        $this->service->shouldReceive('put')->once()->with($this->application)->andReturn(true);
        $this->observer->updated($this->application);
        $this->assertTrue(true);
    }

    public function test_deleted_forgets_application(): void
    {
        $this->service->shouldReceive('forget')->once()->with(31)->andReturn(true);
        $this->observer->deleted($this->application);
        $this->assertTrue(true);
    }

    public function test_cache_exception_does_not_escape_crud(): void
    {
        $this->logger->shouldReceive('warning')->once();
        $this->service->shouldReceive('put')->once()->andThrow(new RuntimeException('redis unavailable'));

        $this->observer->updated($this->application);
        $this->assertTrue(true);
    }

    public function test_service_may_no_op_when_flag_is_off(): void
    {
        $this->service->shouldReceive('put')->once()->andReturn(false);
        $this->observer->updated($this->application);
        $this->assertTrue(true);
    }
}
