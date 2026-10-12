<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\CustomerController;
use App\Models\Customer;
use App\Models\User;
use App\Observers\CustomerObserver;
use App\Policies\CustomerPolicy;
use App\Services\Cache\SureBillsCache;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\Gate;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class CustomerEditCacheTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_flag_off_reads_database_without_cache(): void
    {
        $cache = Mockery::mock(SureBillsCache::class);
        $cache->shouldReceive('enabled')->once()->with('customers')->andReturn(false);
        $cache->shouldReceive('rememberForever')->never();
        $cache->shouldReceive('put')->never();
        $controller = $this->controller($cache, fn () => $this->customer());

        $loaded = $controller->expose(9);

        $this->assertSame('QA Customer', $loaded->name);
        $this->assertSame(1, $controller->lookups);
    }

    public function test_cache_miss_loads_database_and_stores_allow_list(): void
    {
        $stored = null;
        $cache = Mockery::mock(SureBillsCache::class);
        $cache->shouldReceive('enabled')->once()->with('customers')->andReturn(true);
        $cache->shouldReceive('rememberForever')
            ->once()
            ->with('customer:9', Mockery::type('Closure'), 'customers')
            ->andReturnUsing(function ($key, $callback) use (&$stored) {
                $stored = $callback();

                return $stored;
            });
        $controller = $this->controller($cache, fn () => $this->customer());

        $loaded = $controller->expose(9);

        $this->assertSame(CustomerObserver::PAYLOAD_FIELDS, array_keys($stored));
        $this->assertSame('QA Customer', $loaded->name);
        $this->assertSame(4, $loaded->user_id);
        $this->assertSame(1, $controller->lookups);
        $this->assertArrayNotHasKey('bills', $stored);
    }

    public function test_cache_hit_does_not_query_customer(): void
    {
        $payload = CustomerObserver::toPayload($this->customer());
        $payload['name'] = 'Cached Name';
        $cache = Mockery::mock(SureBillsCache::class);
        $cache->shouldReceive('enabled')->once()->andReturn(true);
        $cache->shouldReceive('rememberForever')->once()->andReturn($payload);
        $controller = $this->controller($cache, function () {
            throw new \RuntimeException('database should not be queried');
        });

        $loaded = $controller->expose('9', $this->customer());

        $this->assertInstanceOf(Customer::class, $loaded);
        $this->assertSame('Cached Name', $loaded->name);
        $this->assertSame(4, $loaded->user_id);
        $this->assertSame(0, $controller->lookups);
    }

    public function test_invalid_payload_falls_back_to_database(): void
    {
        $cache = Mockery::mock(SureBillsCache::class);
        $cache->shouldReceive('enabled')->once()->andReturn(true);
        $cache->shouldReceive('rememberForever')->once()->andReturn('not-an-array');
        $controller = $this->controller($cache, fn () => $this->customer());

        $loaded = $controller->expose(9);

        $this->assertSame('QA Customer', $loaded->name);
        $this->assertSame(1, $controller->lookups);
    }

    public function test_missing_customer_is_not_found(): void
    {
        $cache = Mockery::mock(SureBillsCache::class);
        $cache->shouldReceive('enabled')->never();
        $cache->shouldReceive('rememberForever')->never();
        $controller = $this->controller($cache, fn () => null);

        $this->expectException(ModelNotFoundException::class);
        $controller->expose(9);
    }

    public function test_authorized_merchant_is_allowed_on_cache_hit(): void
    {
        $database = $this->customer(123, 10);
        $result = $this->editThroughPolicy(
            merchantId: 10,
            database: $database,
            cachedUserId: 10,
        );

        $this->assertSame('allowed', $result['outcome']);
        $this->assertSame(10, $result['authorized']->user_id);
        $this->assertSame(1, $result['lookups']);
    }

    public function test_unauthorized_merchant_is_denied_on_cache_hit(): void
    {
        $result = $this->editThroughPolicy(
            merchantId: 10,
            database: $this->customer(123, 20),
            cachedUserId: 20,
        );

        $this->assertSame('denied', $result['outcome']);
        $this->assertSame(20, $result['authorized']->user_id);
        $this->assertSame(1, $result['lookups']);
    }

    public function test_cache_owner_cannot_grant_access_to_another_merchants_customer(): void
    {
        $result = $this->editThroughPolicy(
            merchantId: 10,
            database: $this->customer(123, 20),
            cachedUserId: 10,
        );

        $this->assertSame('denied', $result['outcome']);
        $this->assertSame(20, $result['authorized']->user_id);
    }

    public function test_stale_cache_owner_cannot_deny_the_database_owner(): void
    {
        $result = $this->editThroughPolicy(
            merchantId: 10,
            database: $this->customer(123, 10),
            cachedUserId: 20,
        );

        $this->assertSame('allowed', $result['outcome']);
        $this->assertSame(10, $result['authorized']->user_id);
    }

    public function test_missing_database_customer_is_not_found_when_cache_payload_exists(): void
    {
        $result = $this->editThroughPolicy(
            merchantId: 10,
            database: null,
            cachedUserId: 10,
        );

        $this->assertSame('missing', $result['outcome']);
        $this->assertNull($result['authorized']);
        $this->assertSame(1, $result['lookups']);
    }

    public function test_update_and_destroy_still_bind_a_database_customer(): void
    {
        $update = new ReflectionMethod(CustomerController::class, 'update');
        $destroy = new ReflectionMethod(CustomerController::class, 'destroy');
        $edit = new ReflectionMethod(CustomerController::class, 'edit');

        $this->assertSame(Customer::class, $update->getParameters()[1]->getType()->getName());
        $this->assertSame(Customer::class, $destroy->getParameters()[0]->getType()->getName());
        $this->assertNull($edit->getParameters()[0]->getType());
    }

    public function test_index_and_search_methods_do_not_use_the_customer_cache(): void
    {
        $source = file_get_contents(dirname(__DIR__, 4).'/app/Http/Controllers/CustomerController.php');
        foreach (['index', 'searchByName', 'searchByMobile'] as $method) {
            $body = $this->methodBody($source, $method);
            $this->assertStringNotContainsString('rememberForever', $body, $method);
            $this->assertStringNotContainsString('CacheKeys', $body, $method);
            $this->assertStringNotContainsString("enabled('customers')", $body, $method);
        }
    }

    private function controller(SureBillsCache $cache, callable $finder): CustomerController
    {
        return new class($cache, $finder) extends CustomerController
        {
            public int $lookups = 0;

            public function __construct(SureBillsCache $cache, private $finder)
            {
                parent::__construct($cache);
            }

            public function expose(int|string $id, ?Customer $authoritative = null): Customer
            {
                return $this->customerForEdit($id, $authoritative);
            }

            protected function findCustomer(int $id): ?Customer
            {
                $this->lookups++;

                return ($this->finder)($id);
            }
        };
    }

    /**
     * @return array{outcome: string, authorized: ?Customer, lookups: int}
     */
    private function editThroughPolicy(int $merchantId, ?Customer $database, int $cachedUserId): array
    {
        $payload = CustomerObserver::toPayload($database ?? $this->customer(123, $cachedUserId));
        $payload['id'] = 123;
        $payload['user_id'] = $cachedUserId;

        $cache = Mockery::mock(SureBillsCache::class);
        $cache->shouldReceive('enabled')->andReturn(true);
        $cache->shouldReceive('rememberForever')->andReturn($payload);

        $authorized = null;
        $previous = Container::getInstance();
        $container = new Container;
        Container::setInstance($container);

        $gate = new Gate($container, fn () => $this->merchant($merchantId));
        $gate->policy(Customer::class, CustomerPolicy::class);
        $gate->before(function ($user, $ability, $arguments) use (&$authorized) {
            $authorized = $arguments[0] ?? null;

            return null;
        });
        $container->instance(GateContract::class, $gate);

        $controller = $this->controller($cache, fn () => $database);

        try {
            $controller->edit(123);
            $outcome = 'rendered';
        } catch (AuthorizationException) {
            $outcome = 'denied';
        } catch (ModelNotFoundException) {
            $outcome = 'missing';
        } catch (BindingResolutionException) {
            $outcome = 'allowed';
        } finally {
            Container::setInstance($previous);
        }

        return [
            'outcome' => $outcome,
            'authorized' => $authorized,
            'lookups' => $controller->lookups,
        ];
    }

    private function merchant(int $id): User
    {
        $merchant = new User;
        $merchant->setRawAttributes([
            'id' => $id,
            'store_main_user_id' => null,
        ], true);
        $merchant->setRelation('users', new Collection);

        return $merchant;
    }

    private function customer(int $id = 9, int $userId = 4): Customer
    {
        $customer = new Customer;
        $customer->setRawAttributes([
            'id' => $id,
            'name' => 'QA Customer',
            'mobile' => '9690000001',
            'email' => 'qa@example.test',
            'notes' => null,
            'bullding_no' => null,
            'street_name' => null,
            'district' => null,
            'city' => null,
            'postal_code' => null,
            'additional_no' => null,
            'other_buyer_id' => null,
            'vat_registration_number' => null,
            'user_id' => $userId,
        ], true);

        return $customer;
    }

    private function methodBody(string $source, string $method): string
    {
        $pattern = '/function\s+'.$method.'\s*\([^)]*\)\s*\{/';
        if (! preg_match($pattern, $source, $match, PREG_OFFSET_CAPTURE)) {
            $this->fail('Method '.$method.' was not found.');
        }

        $start = $match[0][1] + strlen($match[0][0]);
        $depth = 1;
        $length = strlen($source);
        for ($i = $start; $i < $length; $i++) {
            if ($source[$i] === '{') {
                $depth++;
            } elseif ($source[$i] === '}') {
                $depth--;
                if ($depth === 0) {
                    return substr($source, $start, $i - $start);
                }
            }
        }

        $this->fail('Method '.$method.' body was not closed.');
    }
}
