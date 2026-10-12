<?php

namespace Tests\Unit\Observers;

use App\Models\User;
use App\Observers\UserObserver;
use App\Services\MerchantProfileService;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

class UserObserverMerchantProfileCacheTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private $profiles;
    private $logger;
    private UserObserver $observer;
    private ?Container $previousContainer = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = Facade::getFacadeApplication();
        $app = new Container;
        Container::setInstance($app);
        Facade::setFacadeApplication($app);
        Facade::clearResolvedInstance('log');
        Facade::clearResolvedInstance('events');
        Facade::clearResolvedInstance('auth');

        $this->logger = Mockery::mock()->shouldIgnoreMissing();
        $app->instance('log', $this->logger);

        $events = Mockery::mock()->shouldIgnoreMissing();
        $app->instance('events', $events);

        $guard = Mockery::mock()->shouldIgnoreMissing();
        $guard->shouldReceive('check')->andReturn(false);
        $auth = Mockery::mock()->shouldIgnoreMissing();
        $auth->shouldReceive('guard')->andReturn($guard);
        $app->instance('auth', $auth);

        $this->profiles = Mockery::mock(MerchantProfileService::class);
        $this->observer = new UserObserver($this->profiles);
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstance('log');
        Facade::clearResolvedInstance('events');
        Facade::clearResolvedInstance('auth');
        Facade::setFacadeApplication($this->previousContainer);
        Container::setInstance($this->previousContainer);
        parent::tearDown();
    }

    private function user(string $name = 'Before'): User
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->setRawAttributes([
            'id' => 9,
            'name' => $name,
            'store_main_user_id' => 1,
            'source' => 'sure bills',
            'password' => 'hashed-secret',
        ], true);

        return $user;
    }

    public function test_rename_puts_profile_and_bumps_dropdown_version(): void
    {
        $user = $this->user('Before');
        $user->name = 'After';
        $user->syncChanges();

        $this->profiles->shouldReceive('put')->once()->with($user);
        $this->profiles->shouldReceive('bumpDropdown')->once();

        $this->observer->updated($user);
    }

    public function test_password_only_update_does_not_bump_dropdown(): void
    {
        $user = $this->user('Before');
        $user->password = 'new-hash';
        $user->syncChanges();

        $this->profiles->shouldReceive('put')->once()->with($user);
        $this->profiles->shouldReceive('bumpDropdown')->never();

        $this->observer->updated($user);
    }

    public function test_delete_forgets_profile_and_bumps_dropdown(): void
    {
        $user = $this->user();
        $this->profiles->shouldReceive('forget')->once()->with(9);
        $this->profiles->shouldReceive('bumpDropdown')->once();

        $this->observer->deleted($user);
    }

    public function test_cache_exception_does_not_escape_observer(): void
    {
        $user = $this->user('Before');
        $user->name = 'After';
        $user->syncChanges();
        $this->logger->shouldReceive('warning')->once();
        $this->profiles->shouldReceive('put')->once()->andThrow(new RuntimeException('redis down'));
        $this->profiles->shouldReceive('bumpDropdown')->never();

        try {
            $this->observer->updated($user);
            $escaped = false;
        } catch (Throwable) {
            $escaped = true;
        }

        $this->assertFalse($escaped);
    }
}
