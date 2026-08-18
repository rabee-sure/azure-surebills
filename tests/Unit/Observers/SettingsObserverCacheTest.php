<?php

namespace Tests\Unit\Observers;

use App\Models\Settings;
use App\Observers\SettingsObserver;
use App\Services\MerchantSettingsService;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

/**
 * PR-09: SettingsObserver additive cache duties.
 *
 * Minimal Facade container — no Redis/TranslationServiceProvider bootstrap.
 */
class SettingsObserverCacheTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private MockInterface $settingsCache;

    private MockInterface $logger;

    private SettingsObserver $observer;

    /** @var Settings&MockInterface */
    private $settings;

    private ?Container $previousContainer = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->previousContainer = Facade::getFacadeApplication();
        $app = new Container;
        Facade::setFacadeApplication($app);

        $this->logger = Mockery::mock();
        $this->logger->shouldIgnoreMissing();
        $app->instance('log', $this->logger);

        $this->settingsCache = Mockery::mock(MerchantSettingsService::class);
        $this->observer = new SettingsObserver($this->settingsCache);

        $this->settings = Mockery::mock(Settings::class)->makePartial();
        $this->settings->user_id = 10;
    }

    protected function tearDown(): void
    {
        Facade::setFacadeApplication($this->previousContainer);
        parent::tearDown();
    }

    public function test_created_calls_put_settings(): void
    {
        $this->settingsCache->shouldReceive('putSettings')->once()->with($this->settings)->andReturn(true);
        $this->observer->created($this->settings);
        $this->assertTrue(true);
    }

    public function test_updated_calls_put_settings(): void
    {
        $this->settingsCache->shouldReceive('putSettings')->once()->with($this->settings)->andReturn(true);
        $this->observer->updated($this->settings);
        $this->assertTrue(true);
    }

    public function test_deleted_calls_forget_settings(): void
    {
        $this->settingsCache->shouldReceive('forgetSettings')->once()->with($this->settings)->andReturn(true);
        $this->observer->deleted($this->settings);
        $this->assertTrue(true);
    }

    public function test_cache_exception_does_not_escape_observer(): void
    {
        $this->logger->shouldReceive('warning')->once();
        $this->settingsCache->shouldReceive('putSettings')->once()->andThrow(new RuntimeException('redis down'));

        try {
            $this->observer->created($this->settings);
            $escaped = false;
        } catch (Throwable) {
            $escaped = true;
        }

        $this->assertFalse($escaped);
    }

    public function test_flag_off_is_enforced_inside_service_put(): void
    {
        // Observer always delegates; service no-ops when flag OFF.
        $this->settingsCache->shouldReceive('putSettings')->once()->with($this->settings)->andReturn(false);
        $this->observer->updated($this->settings);
        $this->assertTrue(true);
    }
}
