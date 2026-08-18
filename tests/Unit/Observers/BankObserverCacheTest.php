<?php

namespace Tests\Unit\Observers;

use App\Models\Bank;
use App\Observers\BankObserver;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

/**
 * PR-08: Merchant BankObserver additive cache duties.
 *
 * Pure PHPUnit + Mockery with a minimal Facade container (no Laravel
 * CreatesApplication / Redis / TranslationServiceProvider bootstrap).
 */
class BankObserverCacheTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private MockInterface $cache;

    private MockInterface $logger;

    private BankObserver $observer;

    /** @var Bank&MockInterface */
    private $bank;

    private ?Container $previousContainer = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->previousContainer = Facade::getFacadeApplication();

        $app = new Container;
        Facade::setFacadeApplication($app);

        $guard = new class
        {
            public function check(): bool
            {
                return false;
            }
        };

        $auth = Mockery::mock();
        $auth->shouldReceive('guard')->with('admins')->andReturn($guard);
        $app->instance('auth', $auth);

        $this->logger = Mockery::mock();
        $this->logger->shouldIgnoreMissing();
        $app->instance('log', $this->logger);

        $this->cache = Mockery::mock(SureBillsCache::class);
        $this->observer = new BankObserver($this->cache);

        $this->bank = Mockery::mock(Bank::class)->makePartial();
        $this->bank->id = 5;
        $this->bank->code = 'SNB';
        $this->bank->active = 1;
        $this->bank->sort_number = 10;
        $this->bank->fees = '1.50';
        $this->bank->shouldReceive('getTranslations')
            ->with('name')
            ->andReturn(['en' => 'Bank EN', 'ar' => 'Bank AR']);
    }

    protected function tearDown(): void
    {
        Facade::setFacadeApplication($this->previousContainer);
        parent::tearDown();
    }

    public function test_created_flag_off_skips_cache(): void
    {
        $this->cache->shouldReceive('enabled')->once()->with('banks')->andReturn(false);
        $this->cache->shouldReceive('put')->never();
        $this->cache->shouldReceive('bumpVersion')->never();

        $this->observer->created($this->bank);
        $this->assertTrue(true);
    }

    public function test_updated_flag_off_skips_cache(): void
    {
        $this->cache->shouldReceive('enabled')->once()->with('banks')->andReturn(false);
        $this->cache->shouldReceive('put')->never();
        $this->cache->shouldReceive('bumpVersion')->never();

        $this->observer->updated($this->bank);
        $this->assertTrue(true);
    }

    public function test_deleted_flag_off_skips_cache(): void
    {
        $this->cache->shouldReceive('enabled')->once()->with('banks')->andReturn(false);
        $this->cache->shouldReceive('forget')->never();
        $this->cache->shouldReceive('bumpVersion')->never();

        $this->observer->deleted($this->bank);
        $this->assertTrue(true);
    }

    public function test_created_flag_on_puts_record_and_bumps(): void
    {
        $this->cache->shouldReceive('enabled')->once()->with('banks')->andReturn(true);
        $this->cache->shouldReceive('put')
            ->once()
            ->withArgs(function (string $key, array $payload) {
                return $key === CacheKeys::bank(5)
                    && $payload['id'] === 5
                    && $payload['code'] === 'SNB'
                    && $payload['name'] === ['en' => 'Bank EN', 'ar' => 'Bank AR']
                    && $payload['active'] === 1
                    && $payload['sort_number'] === 10
                    && $payload['fees'] === '1.50'
                    && ! array_key_exists('users', $payload)
                    && ! array_key_exists('users_count', $payload);
            })
            ->andReturn(true);
        $this->cache->shouldReceive('bumpVersion')
            ->once()
            ->with(CacheKeys::banksActiveVersion())
            ->andReturn(2);

        $this->observer->created($this->bank);
        $this->assertTrue(true);
    }

    public function test_updated_flag_on_puts_record_and_bumps(): void
    {
        $this->cache->shouldReceive('enabled')->once()->with('banks')->andReturn(true);
        $this->cache->shouldReceive('put')->once()->with(CacheKeys::bank(5), Mockery::type('array'))->andReturn(true);
        $this->cache->shouldReceive('bumpVersion')->once()->with(CacheKeys::banksActiveVersion())->andReturn(3);

        $this->observer->updated($this->bank);
        $this->assertTrue(true);
    }

    public function test_deleted_flag_on_forgets_record_and_bumps(): void
    {
        $this->cache->shouldReceive('enabled')->once()->with('banks')->andReturn(true);
        $this->cache->shouldReceive('forget')->once()->with(CacheKeys::bank(5))->andReturn(true);
        $this->cache->shouldReceive('bumpVersion')->once()->with(CacheKeys::banksActiveVersion())->andReturn(4);

        $this->observer->deleted($this->bank);
        $this->assertTrue(true);
    }

    public function test_cache_exception_does_not_escape_observer(): void
    {
        $this->logger->shouldReceive('warning')->once();

        $this->cache->shouldReceive('enabled')->once()->with('banks')->andReturn(true);
        $this->cache->shouldReceive('put')->once()->andThrow(new RuntimeException('redis down'));
        $this->cache->shouldReceive('bumpVersion')->never();

        try {
            $this->observer->created($this->bank);
            $escaped = false;
        } catch (Throwable) {
            $escaped = true;
        }

        $this->assertFalse($escaped);
    }

    public function test_record_payload_is_array_with_translation_map(): void
    {
        $payload = BankObserver::recordPayload($this->bank);

        $this->assertIsArray($payload);
        $this->assertSame([
            'id' => 5,
            'code' => 'SNB',
            'name' => ['en' => 'Bank EN', 'ar' => 'Bank AR'],
            'active' => 1,
            'sort_number' => 10,
            'fees' => '1.50',
        ], $payload);
        $this->assertNotInstanceOf(Bank::class, $payload);
    }

    public function test_action_log_path_not_taken_when_admins_guard_unchecked(): void
    {
        // Auth guard check() returns false in setUp — created must not require Auth::user().
        $this->cache->shouldReceive('enabled')->once()->with('banks')->andReturn(false);

        $this->observer->created($this->bank);
        $this->assertTrue(true);
    }
}
