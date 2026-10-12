<?php

namespace Tests\Unit\Observers;

use App\Observers\TransactionObserver;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Facade;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use RuntimeException;

class TransactionObserverBalanceCacheTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private $cache;

    private $database;

    private ?Container $previousContainer = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = Facade::getFacadeApplication();
        $app = new Container;
        Container::setInstance($app);
        Facade::setFacadeApplication($app);
        Facade::clearResolvedInstance('db');
        $this->database = Mockery::mock();
        $app->instance('db', $this->database);
        $this->cache = Mockery::mock(SureBillsCache::class);
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstance('db');
        Facade::setFacadeApplication($this->previousContainer);
        Container::setInstance($this->previousContainer);
        parent::tearDown();
    }

    public function test_flag_off_does_not_schedule_a_forget(): void
    {
        $this->cache->shouldReceive('enabled')->once()->with('merchant_home')->andReturn(false);
        $this->cache->shouldReceive('forget')->never();
        $this->database->shouldReceive('afterCommit')->never();

        $this->schedule(15);
    }

    public function test_forget_is_scheduled_after_commit_for_the_transaction_owner(): void
    {
        $scheduled = null;
        $forgot = null;
        $this->cache->shouldReceive('enabled')->once()->with('merchant_home')->andReturn(true);
        $this->cache->shouldReceive('forget')->once()->andReturnUsing(function ($key) use (&$forgot) {
            $forgot = $key;

            return true;
        });
        $this->database->shouldReceive('afterCommit')->once()->andReturnUsing(function ($callback) use (&$scheduled) {
            $scheduled = $callback;
        });

        $this->schedule(15);

        $this->assertIsCallable($scheduled);
        $this->assertNull($forgot);

        $scheduled();

        $this->assertSame(CacheKeys::uiBalance(15), $forgot);
        $this->assertSame('merchant:15:ui:balance', $forgot);
    }

    public function test_redis_failure_during_forget_does_not_escape_the_callback(): void
    {
        $scheduled = null;
        $this->cache->shouldReceive('enabled')->once()->andReturn(true);
        $this->cache->shouldReceive('forget')->once()->andThrow(new RuntimeException('redis down'));
        $this->database->shouldReceive('afterCommit')->once()->andReturnUsing(function ($callback) use (&$scheduled) {
            $scheduled = $callback;
        });

        $this->schedule(15);

        $scheduled();
        $this->assertTrue(true);
    }

    public function test_scheduling_failure_does_not_escape_the_observer(): void
    {
        $this->cache->shouldReceive('enabled')->once()->andReturn(true);
        $this->cache->shouldReceive('forget')->never();
        $this->database->shouldReceive('afterCommit')->once()->andThrow(new RuntimeException('cannot schedule'));

        $this->schedule(15);
        $this->assertTrue(true);
    }

    public function test_created_still_updates_balance_before_scheduling_the_forget(): void
    {
        $source = file_get_contents(dirname(__DIR__, 3).'/app/Observers/TransactionObserver.php');
        $created = $this->methodBody($source, 'created');
        $schedule = $this->methodBody($source, 'scheduleHomeBalanceForget');

        $this->assertStringContainsString('lockForUpdate()', $created);
        $this->assertStringContainsString('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE', $created);
        $this->assertStringContainsString('transactionLevel() === 0', $created);
        $this->assertStringContainsString('$transaction->user->balance - $transaction->amount', $created);
        $this->assertStringContainsString('$transaction->user->balance + $transaction->amount', $created);
        $this->assertStringContainsString('$user_balance->balance -= $transaction->amount', $created);
        $this->assertStringContainsString('$user_balance->balance += $transaction->amount', $created);
        $this->assertStringContainsString('$user_balance->save();', $created);
        $save = strpos($created, '$user_balance->save();');
        $forget = strpos($created, 'scheduleHomeBalanceForget');
        $this->assertNotFalse($save);
        $this->assertNotFalse($forget);
        $this->assertLessThan($forget, $save);
        $this->assertStringContainsString('DB::afterCommit', $schedule);
        $this->assertStringContainsString('CacheKeys::uiBalance($userId)', $schedule);
        $this->assertStringNotContainsString('homeLastBills', $source);
        $this->assertStringNotContainsString('homeTotals', $source);
        $this->assertStringNotContainsString('scheduleTransactionVersionBump', $created);
        $this->assertStringNotContainsString('txVersion', $source);
        $this->assertStringNotContainsString('bumpVersion', $source);
        $this->assertStringNotContainsString('function updated', $source);
        $this->assertStringNotContainsString('function deleted', $source);
        $this->assertStringNotContainsString('function restored', $source);
        $this->assertStringNotContainsString('function forceDeleted', $source);
    }

    public function test_two_transaction_owners_forget_different_keys(): void
    {
        $keys = [];
        $this->cache->shouldReceive('enabled')->twice()->andReturn(true);
        $this->cache->shouldReceive('forget')->twice()->andReturnUsing(function ($key) use (&$keys) {
            $keys[] = $key;

            return true;
        });
        $this->database->shouldReceive('afterCommit')->twice()->andReturnUsing(function ($callback) {
            $callback();
        });

        $this->schedule(15);
        $this->schedule(88);

        $this->assertSame([
            'merchant:15:ui:balance',
            'merchant:88:ui:balance',
        ], $keys);
    }

    private function schedule($userId): void
    {
        $observer = new TransactionObserver($this->cache);
        $method = new ReflectionMethod(TransactionObserver::class, 'scheduleHomeBalanceForget');
        $method->setAccessible(true);
        $method->invoke($observer, $userId);
    }

    private function methodBody(string $source, string $method): string
    {
        $start = strpos($source, 'function '.$method);
        $this->assertNotFalse($start, $method);
        $next = strlen($source);
        foreach (["\n    public function ", "\n    protected function ", "\n    private function "] as $marker) {
            $candidate = strpos($source, $marker, $start + 10);
            if ($candidate !== false && $candidate < $next) {
                $next = $candidate;
            }
        }

        return substr($source, $start, $next - $start);
    }
}
