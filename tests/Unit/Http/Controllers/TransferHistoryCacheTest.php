<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\TransferController;
use App\Models\Transfer;
use App\Models\User;
use App\Services\BasicSettingsService;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class TransferHistoryCacheTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (config('cache.default') !== 'array') {
            $this->fail('Transfer history cache tests must use the array cache driver.');
        }

        Cache::flush();
        Carbon::setTestNow(Carbon::parse('2026-10-06 15:00:00', 'Asia/Riyadh'));

        config([
            'surebills_cache.enabled' => false,
            'surebills_cache.fail_open' => true,
            'surebills_cache.log_sample_rate' => 0,
            'surebills_cache.domains.transfers' => false,
        ]);

        $this->actingAs($this->merchant(235));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_transfers_ttl_and_domain_default_off(): void
    {
        $config = require config_path('surebills_cache.php');
        $source = file_get_contents(config_path('surebills_cache.php'));

        $this->assertSame(86400, $config['ttl']['transfers']);
        $this->assertFalse($config['domains']['transfers']);
        $this->assertStringContainsString("env('CACHE_DOMAIN_TRANSFERS', false)", $source);
        $this->assertStringContainsString("env('SUREBILLS_CACHE_ENABLED', false)", $source);
    }

    public function test_flag_off_does_not_cache_the_transfer_history(): void
    {
        Cache::spy();
        $controller = $this->harness();

        $rows = $controller->history();

        Cache::shouldNotHaveReceived('remember');
        $this->assertSame(1, $controller->loads);
        $this->assertSame(235, $controller->lastOwner);
        $this->assertSame(41, $rows[0]->id);
        $this->assertNull(Cache::get(CacheKeys::transfers(235)));
    }

    public function test_master_off_does_not_cache_even_if_the_domain_flag_is_on(): void
    {
        config([
            'surebills_cache.enabled' => false,
            'surebills_cache.domains.transfers' => true,
        ]);
        Cache::spy();
        $controller = $this->harness();

        $controller->history();

        Cache::shouldNotHaveReceived('remember');
        $this->assertSame(1, $controller->loads);
        $this->assertNull(Cache::get(CacheKeys::transfers(235)));
    }

    public function test_history_is_cached_and_reused_as_display_rows(): void
    {
        $this->enableTransfers();
        $controller = $this->harness();

        $first = $controller->history();
        $second = $controller->history();
        $cached = Cache::get(CacheKeys::transfers(235));

        $this->assertSame(1, $controller->loads);
        $this->assertSame(235, $controller->lastOwner);
        $this->assertCount(3, $second);
        $this->assertIsObject($first[0]);
        $this->assertIsArray($cached[0]);
        $this->assertSame(
            ['id', 'amount', 'transfer_fees', 'net_amount', 'note', 'date_from_to', 'created_at', 'status'],
            array_keys($cached[0])
        );
        $this->assertSame(41, $second[0]->id);
        $this->assertSame('10.50', $second[0]->amount);
        $this->assertSame('1.25', $second[0]->transfer_fees);
        $this->assertSame('9.25', $second[0]->net_amount);
        $this->assertSame('September cycle owner 235', $second[0]->note);
        $this->assertSame('2026-09-30', $second[0]->date_from_to);
        $this->assertSame('2026-10-01 14:05:06', $second[0]->created_at);
        $this->assertSame('completed', $second[0]->status);
        $this->assertSame('2026-08-15', $cached[1]['date_from_to']);
        $this->assertSame('send_to_sps', $cached[1]['status']);
        $this->assertSame('', $cached[2]['date_from_to']);
        $this->assertNull($cached[2]['note']);
        $this->assertNull($cached[2]['created_at']);
        $this->assertNull($cached[2]['amount']);
        $this->assertSame('canceled', $second[2]->status);
        $encoded = json_encode($cached);
        $this->assertStringNotContainsString('SA4420000000000000000001', $encoded);
        $this->assertStringNotContainsString('Hidden Name', $encoded);
        $this->assertStringNotContainsString('hidden@example.test', $encoded);
        $this->assertStringNotContainsString('secret.pdf', $encoded);
        $this->assertArrayNotHasKey('filters', $cached[0]);
        $this->assertArrayNotHasKey('iban_number', $cached[0]);
        $this->assertArrayNotHasKey('created_by', $cached[0]);
    }

    public function test_history_expires_with_the_configured_ttl_and_rebuilds(): void
    {
        $this->enableTransfers();
        $controller = $this->harness();
        $writtenAt = Carbon::parse('2026-10-06 15:00:00', 'Asia/Riyadh');

        $controller->history();
        $controller->history();
        $this->assertSame(1, $controller->loads);

        Carbon::setTestNow($writtenAt->copy()->addSeconds(86399));
        $controller->history();
        $this->assertSame(1, $controller->loads);

        Carbon::setTestNow($writtenAt->copy()->addSeconds(86400));
        $controller->history();
        $this->assertSame(2, $controller->loads);
        $this->assertNotNull(Cache::get(CacheKeys::transfers(235)));
    }

    public function test_sub_user_shares_the_main_store_history_key(): void
    {
        $this->enableTransfers();
        $this->actingAs($this->merchant(9, 235));
        $subUser = $this->harness();
        $subUser->history();

        $this->assertSame(235, $subUser->lastOwner);
        $this->assertNotNull(Cache::get(CacheKeys::transfers(235)));
        $this->assertNull(Cache::get(CacheKeys::transfers(9)));

        $this->actingAs($this->merchant(11));
        $other = $this->harness();
        $other->history();

        $this->assertSame(11, $other->lastOwner);
        $this->assertSame(1, $other->loads);
        $this->assertNotNull(Cache::get(CacheKeys::transfers(11)));
        $this->assertNotSame(
            Cache::get(CacheKeys::transfers(235))[0]['note'],
            Cache::get(CacheKeys::transfers(11))[0]['note']
        );
    }

    public function test_remember_uses_the_history_key_ttl_and_transfers_domain(): void
    {
        config(['surebills_cache.ttl.transfers' => 86400]);
        $cache = Mockery::mock(SureBillsCache::class);
        $cache->shouldReceive('forget')->never();
        $cache->shouldReceive('remember')->once()->with(
            'merchant:235:transfers',
            86400,
            Mockery::type('Closure'),
            'transfers'
        )->andReturnUsing(function ($key, $ttl, $callback) {
            return $callback();
        });

        $rows = $this->harness($cache)->history();

        $this->assertSame('2026-09-30', $rows[0]->date_from_to);
        $this->assertSame('completed', $rows[0]->status);
    }

    public function test_history_still_returns_rows_when_redis_fails(): void
    {
        $this->enableTransfers();
        Cache::shouldReceive('remember')->once()->andThrow(new RuntimeException('redis down'));

        $controller = $this->harness();
        $rows = $controller->history();

        $this->assertSame(1, $controller->loads);
        $this->assertSame(41, $rows[0]->id);
        $this->assertSame('10.50', $rows[0]->amount);
        $this->assertSame('send_to_sps', $rows[1]->status);
    }

    public function test_history_query_and_write_paths_stay_uncached(): void
    {
        $controller = file_get_contents(base_path('app/Http/Controllers/TransferController.php'));
        $index = $this->methodBody($controller, 'index');
        $loader = $this->methodBody($controller, 'loadTransferHistory');
        $request = $this->methodBody($controller, 'request');
        $store = $this->methodBody($controller, 'store');
        $service = file_get_contents(base_path('app/Services/TransferService.php'));
        $makeTransfer = $this->methodBody($service, 'makeTransfer');
        $observer = file_get_contents(base_path('app/Observers/TransferObserver.php'));

        $this->assertStringContainsString("authorize('show transfers')", $index);
        $this->assertStringContainsString('store_main_user_id ?? auth()->user()->id', $index);
        $this->assertStringContainsString('cachedTransferHistory', $index);
        $this->assertStringContainsString('Transfer::userId($ownerId)->with(\'created_by\')->get()', $loader);
        $this->assertStringNotContainsString('whereDate', $loader);
        $this->assertStringNotContainsString('whereBetween', $loader);
        $this->assertStringNotContainsString('orderBy', $loader);
        $this->assertSame(1, substr_count($controller, 'CacheKeys::transfers('));

        foreach ([$request, $store, $makeTransfer, $observer] as $body) {
            $this->assertStringNotContainsString('CacheKeys::transfers(', $body);
            $this->assertStringNotContainsString('SureBillsCache', $body);
            $this->assertStringNotContainsString('remember', $body);
        }
    }

    private function enableTransfers(): void
    {
        config([
            'surebills_cache.enabled' => true,
            'surebills_cache.fail_open' => true,
            'surebills_cache.log_sample_rate' => 0,
            'surebills_cache.domains.transfers' => true,
            'surebills_cache.ttl.transfers' => 86400,
        ]);
    }

    /**
     * @param  SureBillsCache|Mockery\MockInterface|null  $cache
     */
    private function harness($cache = null): TransferHistoryCacheHarness
    {
        return new TransferHistoryCacheHarness(
            Mockery::mock(BasicSettingsService::class),
            $cache ?: $this->app->make(SureBillsCache::class)
        );
    }

    /**
     * @param  int|string  $id
     * @param  int|string|null  $mainStoreUserId
     */
    private function merchant($id, $mainStoreUserId = null): User
    {
        $user = new User;
        $user->forceFill([
            'id' => $id,
            'store_main_user_id' => $mainStoreUserId,
        ]);

        return $user;
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

class TransferHistoryCacheHarness extends TransferController
{
    /** @var int */
    public $loads = 0;

    /** @var int|string|null */
    public $lastOwner;

    public function history(): array
    {
        return $this->cachedTransferHistory(auth()->user()->store_main_user_id ?? auth()->user()->id);
    }

    protected function loadTransferHistory($ownerId)
    {
        $this->loads++;
        $this->lastOwner = $ownerId;

        $completed = new Transfer;
        $completed->forceFill([
            'id' => 41,
            'amount' => '10.50',
            'transfer_fees' => '1.25',
            'net_amount' => '9.25',
            'note' => 'September cycle owner '.$ownerId,
            'status' => 'completed',
            'iban_number' => 'SA4420000000000000000001',
            'beneficiary_name' => 'Hidden Name',
            'attachment' => 'secret.pdf',
            'filters' => ['date' => ['to' => '2026-09-30 21:00:00', 'cycle_date' => '2026-09-01']],
            'created_at' => '2026-10-01 14:05:06',
        ]);
        $creator = new User;
        $creator->forceFill([
            'id' => 3,
            'email' => 'hidden@example.test',
        ]);
        $completed->setRelation('created_by', $creator);

        $pending = new Transfer;
        $pending->forceFill([
            'id' => 42,
            'amount' => '4.00',
            'transfer_fees' => '0.50',
            'net_amount' => '3.50',
            'note' => 'cycle only',
            'status' => 'send_to_sps',
            'filters' => ['date' => ['cycle_date' => '2026-08-15']],
            'created_at' => '2026-08-16 08:00:00',
        ]);

        $canceled = new Transfer;
        $canceled->forceFill([
            'id' => 43,
            'amount' => null,
            'transfer_fees' => null,
            'net_amount' => null,
            'note' => null,
            'status' => 'canceled',
            'filters' => null,
            'created_at' => null,
        ]);

        return collect([$completed, $pending, $canceled]);
    }
}
