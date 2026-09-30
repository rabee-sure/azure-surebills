<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\Cache\CacheKeys;
use App\Services\MerchantProfileService;
use App\Services\Cache\SureBillsCache;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class MerchantProfileServiceCacheTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private $cache;
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cache = Mockery::mock(SureBillsCache::class);
        $this->service = Mockery::mock(MerchantProfileService::class, [$this->cache])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    private function user(): User
    {
        $user = new User;
        $user->setRawAttributes([
            'id' => 9,
            'name' => 'Acme',
            'email' => 'acme@example.test',
            'mobile' => '500000000',
            'mobile_verified' => 1,
            'business_name_en' => 'Acme EN',
            'business_name_ar' => 'Acme AR',
            'logo' => 'logos/acme.png',
            'verified' => true,
            'store_main_user_id' => null,
            'source' => 'sure bills',
            'password' => 'hashed-secret',
            'remember_token' => 'remember-secret',
            'mobile_active_code' => '0000',
            'iban_number' => 'SA00SECRET',
            'mada_percentage' => '1.70',
            'zatca_token' => 'zatca-secret',
            'zatca_secret' => 'zatca-secret',
            'two_factor_secret' => 'otp-secret',
        ], true);

        return $user;
    }

    public function test_flag_off_reads_database_only(): void
    {
        $user = $this->user();
        $this->cache->shouldReceive('enabled')->once()->with('merchant_profile')->andReturn(false);
        $this->cache->shouldReceive('rememberForever')->never();
        $this->service->shouldReceive('loadFromDatabase')->once()->with(9)->andReturn($user);

        $this->assertSame($user, $this->service->get(9));
    }

    public function test_cold_cache_uses_profile_key_and_sanitized_payload(): void
    {
        $user = $this->user();
        $this->cache->shouldReceive('enabled')->once()->with('merchant_profile')->andReturn(true);
        $this->service->shouldReceive('loadFromDatabase')->once()->with(9)->andReturn($user);
        $this->cache->shouldReceive('rememberForever')
            ->once()
            ->with(CacheKeys::merchantProfile(9), Mockery::type('Closure'), 'merchant_profile')
            ->andReturnUsing(fn ($key, $callback) => $callback());
        $this->service->shouldReceive('hydrate')->once()->andReturnUsing(
            fn (array $payload) => (new User())->forceFill($payload)
        );

        $loaded = $this->service->get(9);

        $this->assertSame('Acme', $loaded->name);
        $this->assertNull($loaded->password);
        $this->assertNull($loaded->remember_token);
        $this->assertNull($loaded->iban_number);
        $this->assertNull($loaded->zatca_token);
    }

    public function test_payload_allow_list_excludes_secrets_and_fees(): void
    {
        $payload = MerchantProfileService::toPayload($this->user());

        $this->assertSame(MerchantProfileService::PAYLOAD_FIELDS, array_keys($payload));
        $this->assertSame('Acme', $payload['name']);
        foreach ([
            'password',
            'remember_token',
            'mobile_active_code',
            'iban_number',
            'mada_percentage',
            'zatca_token',
            'zatca_secret',
            'two_factor_secret',
        ] as $forbidden) {
            $this->assertArrayNotHasKey($forbidden, $payload);
        }
    }

    public function test_put_and_bump_are_inert_when_flag_off(): void
    {
        $this->cache->shouldReceive('enabled')->twice()->with('merchant_profile')->andReturn(false);
        $this->cache->shouldReceive('put')->never();
        $this->cache->shouldReceive('bumpVersion')->never();

        $this->assertFalse($this->service->put($this->user()));
        $this->assertSame(0, $this->service->bumpDropdown());
    }

    public function test_profile_key_matches_shared_contract(): void
    {
        $this->assertSame('merchant:9:profile', CacheKeys::merchantProfile(9));
        $this->assertSame('merchants:dropdown:version', CacheKeys::merchantsDropdownVersion());
        $this->assertSame('merchants:dropdown:v3', CacheKeys::merchantsDropdown(3));
    }
}
