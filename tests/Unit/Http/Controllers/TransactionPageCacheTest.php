<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\StatementController;
use App\Models\Application;
use App\Models\Bill;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class TransactionPageCacheTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (config('cache.default') !== 'array') {
            $this->fail('Transaction page cache tests must use the array cache driver.');
        }

        Cache::flush();
        Carbon::setTestNow(Carbon::parse('2026-10-06 15:00:00', 'Asia/Riyadh'));

        config([
            'surebills_cache.enabled' => false,
            'surebills_cache.fail_open' => true,
            'surebills_cache.log_sample_rate' => 0,
            'surebills_cache.domains.merchant_transactions' => false,
        ]);

        $this->actingAs($this->merchant(235));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_transactions_ttl_and_domain_default_off(): void
    {
        $config = require config_path('surebills_cache.php');

        $this->assertSame(60, $config['ttl']['transactions']);
        $this->assertFalse($config['domains']['merchant_transactions']);
        $this->assertStringContainsString(
            "env('SUREBILLS_CACHE_ENABLED', false)",
            file_get_contents(config_path('surebills_cache.php'))
        );
    }

    public function test_flag_off_does_not_cache_the_statement_page(): void
    {
        Cache::spy();
        $controller = $this->statementHarness();

        $page = $controller->resolve($this->request('/statement'));

        Cache::shouldNotHaveReceived('remember');
        $this->assertSame(1, $controller->loads);
        $this->assertFalse($controller->withRelations);
        $this->assertSame(12.5, $page[1]['credit']);
        $this->assertNull(Cache::get($this->statementKey(235, 1)));
    }

    public function test_master_off_does_not_cache_even_if_the_domain_flag_is_on(): void
    {
        config([
            'surebills_cache.enabled' => false,
            'surebills_cache.domains.merchant_transactions' => true,
        ]);
        Cache::spy();
        $controller = $this->statementHarness();

        $controller->resolve($this->request('/statement'));

        Cache::shouldNotHaveReceived('remember');
        $this->assertFalse($controller->withRelations);
    }

    public function test_default_statement_page_is_cached_and_reused(): void
    {
        $this->enableTransactions();
        $controller = $this->statementHarness();
        $request = $this->request('/statement');

        app()->setLocale('en');
        $first = $controller->resolve($request);
        $englishName = $first[0]->first()->bill->user->mainStoreUser->business_name;
        app()->setLocale('ar');
        $second = $controller->resolve($request);
        $arabicName = $second[0]->first()->bill->user->mainStoreUser->business_name;

        $this->assertSame(1, $controller->loads);
        $this->assertTrue($controller->withRelations);
        $this->assertSame(50, $first[0]->perPage());
        $this->assertSame(12.5, $second[1]['credit']);
        $this->assertSame(2.5, $second[1]['debit']);
        $this->assertSame(10.0, $second[1]['all']);
        $this->assertSame('page-1', $second[0]->first()->description);
        $this->assertSame(7, $second[0]->first()->bill->application_id);
        $this->assertSame(3, $second[0]->first()->bill->application->channel_id);
        $this->assertSame('Main Store', $englishName);
        $this->assertSame('المتجر', $arabicName);
        $cached = Cache::get($this->statementKey(235, 1));
        $this->assertSame(12.5, $cached['totals']['credit']);
        $this->assertArrayNotHasKey('header_balance', $cached);
        $this->assertArrayNotHasKey('balance', $cached['totals']);
    }

    public function test_statement_page_expires_with_the_configured_ttl_and_rebuilds(): void
    {
        $this->enableTransactions();
        $controller = $this->statementHarness();
        $request = $this->request('/statement');
        $writtenAt = Carbon::parse('2026-10-06 15:00:00', 'Asia/Riyadh');

        $controller->resolve($request);
        $controller->resolve($request);
        $this->assertSame(1, $controller->loads);

        Carbon::setTestNow($writtenAt->copy()->addSeconds(59));
        $controller->resolve($request);
        $this->assertSame(1, $controller->loads);

        Carbon::setTestNow($writtenAt->copy()->addSeconds(60));
        $controller->resolve($request);
        $this->assertSame(2, $controller->loads);
        $this->assertNotNull(Cache::get($this->statementKey(235, 1)));
    }

    public function test_statement_pages_and_api_pages_do_not_share_a_key(): void
    {
        $this->enableTransactions();
        $statement = $this->statementHarness();
        $api = $this->apiHarness();

        $statement->resolve($this->request('/statement', ['page' => 1]));
        $statement->resolve($this->request('/statement', ['page' => 2]));
        $api->index($this->request('/api/v1/transactions', ['page' => 1]));
        $api->index($this->request('/api/v1/transactions', ['page' => 2]));

        $this->assertNotNull(Cache::get($this->statementKey(235, 1)));
        $this->assertNotNull(Cache::get($this->statementKey(235, 2)));
        $this->assertNotNull(Cache::get($this->apiKey(235, 1)));
        $this->assertNotNull(Cache::get($this->apiKey(235, 2)));
        $this->assertNotSame(
            Cache::get($this->statementKey(235, 1))['items'][0]['description'],
            Cache::get($this->statementKey(235, 2))['items'][0]['description']
        );
        $this->assertNotSame(
            Cache::get($this->apiKey(235, 1))['meta']['current_page'],
            Cache::get($this->apiKey(235, 2))['meta']['current_page']
        );
        $this->assertSame(2, $statement->loads);
        $this->assertSame(2, $api->loads);

        $api->index($this->request('/api/v1/transactions', ['page' => 1]));
        $statement->resolve($this->request('/statement', ['page' => 1]));
        $this->assertSame(2, $api->loads);
        $this->assertSame(2, $statement->loads);
    }

    public function test_sub_user_uses_the_main_store_statement_key(): void
    {
        $this->enableTransactions();
        $this->actingAs($this->merchant(9, 235));
        $controller = $this->statementHarness();

        $controller->resolve($this->request('/statement'));

        $this->assertNotNull(Cache::get($this->statementKey(235, 1)));
        $this->assertNull(Cache::get($this->statementKey(9, 1)));
    }

    public function test_explicit_month_to_date_is_cached_and_other_windows_are_not(): void
    {
        $this->enableTransactions();
        $controller = $this->statementHarness();

        $this->assertTrue($controller->cacheable($this->request('/statement', [
            'date_start' => '10/01/2026',
            'date_to' => '10/06/2026',
            'transaction_type' => 'all',
            'transaction_source' => 'all',
            'channel_id' => 'all',
            'application_id' => 'undefined',
        ])));

        Cache::spy();
        foreach ([
            ['date_start' => '09/01/2026', 'date_to' => '09/30/2026'],
            ['date_start' => '10/01/2026', 'date_to' => '10/31/2026'],
            ['date_start' => '10/02/2026', 'date_to' => '10/06/2026'],
            ['transaction_type' => 'debit'],
            ['transaction_type' => 'credit'],
            ['transaction_source' => 'refund'],
            ['channel_id' => '4'],
            ['application_id' => '9'],
            ['search' => 'fee'],
        ] as $query) {
            $controller->resolve($this->request('/statement', $query));
        }

        Cache::shouldNotHaveReceived('remember');
        $this->assertSame(9, $controller->loads);
        $this->assertFalse($controller->withRelations);
    }

    public function test_statement_remember_uses_the_transactions_ttl_and_stable_page_key(): void
    {
        config(['surebills_cache.ttl.transactions' => 60]);
        $cache = Mockery::mock(SureBillsCache::class);
        $cache->shouldReceive('enabled')->once()->with('merchant_transactions')->andReturn(true);
        $cache->shouldReceive('versionedKey')->never();
        $cache->shouldReceive('remember')->once()->with(
            'merchant:235:tx:202610:p1',
            60,
            Mockery::type('Closure'),
            'merchant_transactions'
        )->andReturnUsing(function ($key, $ttl, $callback) {
            return $callback();
        });

        $controller = $this->statementHarness($cache);
        $page = $controller->resolve($this->request('/statement'));

        $this->assertSame('page-1', $page[0]->first()->description);
    }

    public function test_statement_still_renders_when_redis_fails(): void
    {
        $this->enableTransactions();
        Cache::shouldReceive('remember')->once()->andThrow(new RuntimeException('redis down'));

        $controller = $this->statementHarness();
        $page = $controller->resolve($this->request('/statement'));

        $this->assertSame(1, $controller->loads);
        $this->assertSame('page-1', $page[0]->first()->description);
        $this->assertSame(10.0, $page[1]['all']);
    }

    public function test_api_default_page_is_cached_and_filters_stay_on_the_database_path(): void
    {
        $this->enableTransactions();
        $controller = $this->apiHarness();

        $first = $controller->index($this->request('/api/v1/transactions'));
        $second = $controller->index($this->request('/api/v1/transactions', ['per_page' => '10']));

        $this->assertSame(1, $controller->loads);
        $this->assertSame(5, $first->getData(true)['meta']['balance']);
        $this->assertSame(8, $second->getData(true)['meta']['total_credit']);
        $this->assertSame(3, $second->getData(true)['meta']['total_debit']);
        $this->assertSame(10, $second->getData(true)['meta']['per_page']);

        foreach ([
            ['per_page' => '20'],
            ['transaction_type' => 'debit'],
            ['transaction_source' => 'bill'],
            ['channel' => '4'],
            ['date_start' => '09/01/2026', 'date_to' => '09/30/2026'],
            ['date_to' => '10/31/2026'],
            ['search' => 'fee'],
        ] as $query) {
            $live = $controller->index($this->request('/api/v1/transactions', $query));
            $this->assertSame('live', $live->getData(true)['data'][0]['id']);
        }

        $this->assertSame(8, $controller->loads);
        $this->assertSame(5, Cache::get($this->apiKey(235, 1))['meta']['balance']);
    }

    public function test_api_remember_uses_the_api_suffix_and_transactions_ttl(): void
    {
        config(['surebills_cache.ttl.transactions' => 60]);
        $cache = Mockery::mock(SureBillsCache::class);
        $cache->shouldReceive('enabled')->once()->with('merchant_transactions')->andReturn(true);
        $cache->shouldReceive('versionedKey')->never();
        $cache->shouldReceive('remember')->once()->with(
            'merchant:235:tx:202610:p2:api',
            60,
            Mockery::type('Closure'),
            'merchant_transactions'
        )->andReturnUsing(function ($key, $ttl, $callback) {
            return $callback();
        });

        $controller = $this->apiHarness($cache);
        $response = $controller->index($this->request('/api/v1/transactions', ['page' => 2]));

        $this->assertSame(2, $response->getData(true)['meta']['current_page']);
    }

    public function test_api_still_returns_json_when_redis_fails(): void
    {
        $this->enableTransactions();
        Cache::shouldReceive('remember')->once()->andThrow(new RuntimeException('redis down'));

        $controller = $this->apiHarness();
        $response = $controller->index($this->request('/api/v1/transactions'));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(5, $response->getData(true)['meta']['balance']);
        $this->assertSame(1, $controller->loads);
    }

    public function test_missing_auth_user_does_not_read_the_transaction_cache(): void
    {
        $this->enableTransactions();
        auth()->logout();
        Cache::spy();
        $controller = $this->apiHarness();

        $response = $controller->index($this->request('/api/v1/transactions'));

        Cache::shouldNotHaveReceived('remember');
        $this->assertSame('live', $response->getData(true)['data'][0]['id']);
    }

    public function test_export_and_statement_query_helpers_are_not_cached(): void
    {
        $controller = file_get_contents(base_path('app/Http/Controllers/StatementController.php'));
        $export = file_get_contents(base_path('app/Exports/StatementExport.php'));
        $user = file_get_contents(base_path('app/Models/User.php'));
        $exportMethod = substr($controller, strpos($controller, 'function export'));

        $this->assertStringNotContainsString('remember', $exportMethod);
        $this->assertStringNotContainsString('SureBillsCache', $export);
        $this->assertStringNotContainsString('SureBillsCache', $user);
        $this->assertStringContainsString('->paginate(50)', $controller);
        $this->assertStringContainsString("paginate(\$request->get('per_page', 10))", file_get_contents(
            base_path('app/Http/Controllers/Api/TransactionController.php')
        ));
    }

    private function enableTransactions(): void
    {
        config([
            'surebills_cache.enabled' => true,
            'surebills_cache.fail_open' => true,
            'surebills_cache.log_sample_rate' => 0,
            'surebills_cache.domains.merchant_transactions' => true,
            'surebills_cache.ttl.transactions' => 60,
        ]);
    }

    /**
     * @param  SureBillsCache|Mockery\MockInterface|null  $cache
     */
    private function statementHarness($cache = null): StatementPageCacheHarness
    {
        return new StatementPageCacheHarness($cache ?: $this->app->make(SureBillsCache::class));
    }

    /**
     * @param  SureBillsCache|Mockery\MockInterface|null  $cache
     */
    private function apiHarness($cache = null): ApiTransactionPageCacheHarness
    {
        return new ApiTransactionPageCacheHarness($cache ?: $this->app->make(SureBillsCache::class));
    }

    private function request(string $path, array $query = []): Request
    {
        $request = Request::create($path, 'GET', $query);
        $this->app->instance('request', $request);

        return $request;
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

    /**
     * @param  int|string  $uid
     */
    private function statementKey($uid, int $page): string
    {
        return CacheKeys::txPage($uid, '202610', $page);
    }

    /**
     * @param  int|string  $uid
     */
    private function apiKey($uid, int $page): string
    {
        return CacheKeys::txPage($uid, '202610', $page).':api';
    }
}

class StatementPageCacheHarness extends StatementController
{
    /** @var int */
    public $loads = 0;

    /** @var bool|null */
    public $withRelations;

    public function resolve(Request $request): array
    {
        return $this->resolveStatementPage($request);
    }

    public function cacheable(Request $request): bool
    {
        return $this->statementPageIsCacheable($request);
    }

    protected function loadStatementPage(bool $withDisplayRelations): array
    {
        $this->loads++;
        $this->withRelations = $withDisplayRelations;
        $page = Paginator::resolveCurrentPage();

        $transaction = new Transaction;
        $transaction->forceFill([
            'id' => 'tx-'.$page,
            'description' => 'page-'.$page,
            'reference' => '100',
            'receipt' => '100000000001',
            'type' => 'credit',
            'amount' => 12.5,
            'balance' => 12.5,
            'card' => '1234',
            'card_brand' => 'MADA',
            'created_at' => '2026-10-02 09:00:00',
        ]);

        $bill = new Bill;
        $bill->forceFill(['application_id' => 7]);
        $application = new Application;
        $application->forceFill(['channel_id' => 3]);
        $owner = new User;
        $owner->forceFill([
            'business_name_en' => 'North Shop',
            'business_name_ar' => 'متجر',
        ]);
        $main = new User;
        $main->forceFill([
            'business_name_en' => 'Main Store',
            'business_name_ar' => 'المتجر',
        ]);
        $owner->setRelation('mainStoreUser', $main);
        $bill->setRelation('application', $application);
        $bill->setRelation('user', $owner);
        $transaction->setRelation('bill', $bill);

        return [
            new LengthAwarePaginator([$transaction], 60, 50, $page),
            ['credit' => 12.5, 'debit' => 2.5, 'all' => 10.0],
        ];
    }
}

class ApiTransactionPageCacheHarness extends TransactionController
{
    /** @var int */
    public $loads = 0;

    public function index(Request $request)
    {
        return parent::index($request);
    }

    protected function apiDisplayPayload(Request $request): array
    {
        $this->loads++;

        return [
            'data' => [['id' => 'tx-1', 'amount' => 5]],
            'links' => ['prev' => null, 'next' => null],
            'meta' => [
                'current_page' => Paginator::resolveCurrentPage(),
                'per_page' => 10,
                'total' => 11,
                'balance' => 5,
                'total_credit' => 8,
                'total_debit' => 3,
            ],
        ];
    }

    protected function apiResourceResponse(Request $request)
    {
        $this->loads++;

        return response()->json([
            'data' => [['id' => 'live']],
            'meta' => [
                'balance' => 1,
                'total_credit' => 1,
                'total_debit' => 0,
            ],
        ]);
    }
}
