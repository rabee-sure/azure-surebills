<?php

namespace Tests\Unit\Services;

use App\Models\Settings;
use App\Models\User;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use App\Services\MerchantSettingsService;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

/**
 * PR-09: MerchantSettingsService (display/form-defaults cache).
 *
 * Pure PHPUnit + Mockery; no Laravel Redis/DB bootstrap.
 */
class MerchantSettingsServiceCacheTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private MockInterface $cache;

    /** @var MerchantSettingsService&MockInterface */
    private $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cache = Mockery::mock(SureBillsCache::class);
        $this->service = Mockery::mock(MerchantSettingsService::class, [$this->cache])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    /**
     * @return Settings&MockInterface
     */
    private function mockSettings(int $userId): Settings
    {
        $settings = Mockery::mock(Settings::class)->shouldIgnoreMissing();
        $settings->shouldReceive('getAttribute')->with('user_id')->andReturn($userId);
        $settings->shouldReceive('getAttribute')->with('add_tax')->andReturn(true);
        $settings->shouldReceive('getAttribute')->with('tax_value')->andReturn(15);
        $settings->shouldReceive('getAttribute')->with('add_tax_invoice')->andReturn(false);
        $settings->shouldReceive('getAttribute')->with('default_lang')->andReturn('ar');
        $settings->shouldReceive('getAttribute')->with('active_lang')->andReturn('all');
        $settings->shouldReceive('getAttribute')->with('create_send_sms')->andReturn(false);
        $settings->shouldReceive('getAttribute')->with('create_send_email')->andReturn(true);
        $settings->shouldReceive('getAttribute')->with('paid_send_sms')->andReturn(false);
        $settings->shouldReceive('getAttribute')->with('paid_send_email')->andReturn(true);
        $settings->shouldReceive('getAttribute')->with('display_customer_details')->andReturn(true);
        $settings->shouldReceive('getAttribute')->with('api_bill_style')->andReturn(false);
        $settings->shouldReceive('getAttribute')->with('background_color_body')->andReturn('#fafafa');
        $settings->shouldReceive('getAttribute')->with('background_image_file')->andReturn(null);
        $settings->shouldReceive('getAttribute')->with('text_color_body')->andReturn('#000000');
        $settings->shouldReceive('getAttribute')->with('background_color_payment_button')->andReturn('#00d595');
        $settings->shouldReceive('getAttribute')->with('text_color_payment_button')->andReturn('#ffffff');
        $settings->shouldReceive('offsetExists')->andReturn(true);
        $settings->shouldReceive('getTranslations')->with('header_bill')->andReturn(['en' => 'Header EN', 'ar' => 'Header AR']);
        $settings->shouldReceive('getTranslations')->with('footer_bill')->andReturn(['en' => 'Footer EN', 'ar' => 'Footer AR']);

        return $settings;
    }

    public function test_flag_off_uses_database_loader(): void
    {
        $settings = $this->mockSettings(10);

        $this->cache->shouldReceive('enabled')->once()->with('merchant_settings')->andReturn(false);
        $this->cache->shouldReceive('rememberForever')->never();
        $this->service->shouldReceive('loadFromDatabase')->once()->with(10)->andReturn($settings);

        $result = $this->service->getForOwner(10);

        $this->assertSame($settings, $result);
    }

    public function test_flag_on_cold_uses_remember_forever_and_cache_keys(): void
    {
        $settings = $this->mockSettings(10);
        $hydrated = Mockery::mock(Settings::class);

        $this->cache->shouldReceive('enabled')->once()->with('merchant_settings')->andReturn(true);
        $this->service->shouldReceive('loadFromDatabase')->once()->with(10)->andReturn($settings);

        $this->cache->shouldReceive('rememberForever')
            ->once()
            ->withArgs(function (string $key, $callback, ?string $domain) {
                return $key === CacheKeys::merchantSettings(10)
                    && $domain === 'merchant_settings'
                    && is_callable($callback);
            })
            ->andReturnUsing(function (string $key, $callback) {
                return $callback();
            });

        $this->service->shouldReceive('hydrate')
            ->once()
            ->with(Mockery::on(function (array $payload) {
                return $payload['user_id'] === 10
                    && $payload['add_tax'] === true
                    && $payload['tax_value'] === 15
                    && $payload['header_bill'] === ['en' => 'Header EN', 'ar' => 'Header AR']
                    && $payload['footer_bill'] === ['en' => 'Footer EN', 'ar' => 'Footer AR']
                    && ! array_key_exists('user', $payload)
                    && ! array_key_exists('relations', $payload);
            }))
            ->andReturn($hydrated);

        $result = $this->service->getForOwner(10);
        $this->assertSame($hydrated, $result);
    }

    public function test_flag_on_warm_skips_database_loader(): void
    {
        $cached = [
            'user_id' => 10,
            'add_tax' => false,
            'tax_value' => 0,
            'add_tax_invoice' => false,
            'default_lang' => 'en',
            'active_lang' => 'en',
            'create_send_sms' => false,
            'create_send_email' => false,
            'paid_send_sms' => false,
            'paid_send_email' => false,
            'header_bill' => ['en' => 'H'],
            'footer_bill' => ['en' => 'F'],
            'display_customer_details' => true,
            'api_bill_style' => false,
            'background_color_body' => null,
            'background_image_file' => null,
            'text_color_body' => null,
            'background_color_payment_button' => null,
            'text_color_payment_button' => null,
        ];
        $hydrated = Mockery::mock(Settings::class);

        $this->cache->shouldReceive('enabled')->once()->with('merchant_settings')->andReturn(true);
        $this->service->shouldReceive('loadFromDatabase')->never();
        $this->cache->shouldReceive('rememberForever')
            ->once()
            ->with(CacheKeys::merchantSettings(10), Mockery::type('Closure'), 'merchant_settings')
            ->andReturn($cached);
        $this->service->shouldReceive('hydrate')->once()->with($cached)->andReturn($hydrated);

        $this->assertSame($hydrated, $this->service->getForOwner(10));
    }

    public function test_payload_shape_includes_tax_and_translation_maps(): void
    {
        $settings = $this->mockSettings(7);
        $payload = MerchantSettingsService::toPayload($settings);

        $this->assertSame(7, $payload['user_id']);
        $this->assertTrue($payload['add_tax']);
        $this->assertSame(15, $payload['tax_value']);
        $this->assertIsArray($payload['header_bill']);
        $this->assertIsArray($payload['footer_bill']);
        $this->assertArrayNotHasKey('user', $payload);
        $this->assertArrayNotHasKey('created_at', $payload);
        $this->assertArrayNotHasKey('updated_at', $payload);
    }

    public function test_resolve_owner_id_main_user(): void
    {
        $user = Mockery::mock(User::class)->shouldIgnoreMissing();
        $user->shouldReceive('getAttribute')->with('store_main_user_id')->andReturn(null);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(10);
        $user->store_main_user_id = null;
        $user->id = 10;

        $service = new MerchantSettingsService($this->cache);
        $this->assertSame(10, $service->resolveOwnerId($user));
    }

    public function test_resolve_owner_id_sub_user(): void
    {
        $user = new User;
        $user->id = 55;
        $user->store_main_user_id = 10;

        $service = new MerchantSettingsService($this->cache);
        $this->assertSame(10, $service->resolveOwnerId($user));
    }

    public function test_two_merchants_produce_isolated_keys(): void
    {
        $this->assertNotSame(
            CacheKeys::merchantSettings(10),
            CacheKeys::merchantSettings(20)
        );
        $this->assertSame('merchant:10:settings', CacheKeys::merchantSettings(10));
        $this->assertSame('merchant:20:settings', CacheKeys::merchantSettings(20));
    }

    public function test_null_store_main_user_id_falls_back_to_user_id(): void
    {
        $user = new User;
        $user->id = 42;
        $user->store_main_user_id = null;

        $service = new MerchantSettingsService($this->cache);
        $this->assertSame(42, $service->resolveOwnerId($user));
    }

    public function test_put_settings_uses_cache_keys_when_flag_on(): void
    {
        $settings = $this->mockSettings(10);

        $this->cache->shouldReceive('enabled')->once()->with('merchant_settings')->andReturn(true);
        $this->cache->shouldReceive('put')
            ->once()
            ->with(CacheKeys::merchantSettings(10), Mockery::type('array'))
            ->andReturn(true);

        $service = new MerchantSettingsService($this->cache);
        $this->assertTrue($service->putSettings($settings));
    }

    public function test_put_settings_flag_off_skips_cache(): void
    {
        $settings = $this->mockSettings(10);
        $this->cache->shouldReceive('enabled')->once()->with('merchant_settings')->andReturn(false);
        $this->cache->shouldReceive('put')->never();

        $service = new MerchantSettingsService($this->cache);
        $this->assertFalse($service->putSettings($settings));
    }

    public function test_service_is_not_referenced_by_api_bill_controller_source(): void
    {
        $source = file_get_contents(dirname(__DIR__, 2) . '/../app/Http/Controllers/Api/BillController.php');
        $this->assertIsString($source);
        $this->assertStringNotContainsString('MerchantSettingsService', $source);
        $this->assertStringContainsString('$user->settings->tax_value', $source);
    }

    public function test_no_physical_redis_key_hardcoding_in_service(): void
    {
        $source = file_get_contents(dirname(__DIR__, 2) . '/../app/Services/MerchantSettingsService.php');
        $this->assertStringNotContainsString('sb_database_', $source);
        $this->assertStringNotContainsString('sb_merchant:', $source);
        $this->assertStringContainsString('CacheKeys::merchantSettings', $source);
    }
}
