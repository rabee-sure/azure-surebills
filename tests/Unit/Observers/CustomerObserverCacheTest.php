<?php

namespace Tests\Unit\Observers;

use App\Models\Customer;
use App\Observers\CustomerObserver;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

class CustomerObserverCacheTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private $cache;
    private $logger;
    private CustomerObserver $observer;
    private ?Container $previousContainer = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = Facade::getFacadeApplication();
        $app = new Container;
        Container::setInstance($app);
        Facade::setFacadeApplication($app);
        Facade::clearResolvedInstance('log');
        $this->logger = Mockery::mock()->shouldIgnoreMissing();
        $app->instance('log', $this->logger);
        $this->cache = Mockery::mock(SureBillsCache::class);
        $this->observer = new CustomerObserver($this->cache);
    }

    protected function tearDown(): void
    {
        Facade::clearResolvedInstance('log');
        Facade::setFacadeApplication($this->previousContainer);
        Container::setInstance($this->previousContainer);
        parent::tearDown();
    }

    public function test_payload_allow_list_excludes_relations(): void
    {
        $customer = $this->customer();
        $customer->setRelation('bills', collect([['id' => 1]]));
        $customer->setRelation('user', new \App\Models\User);

        $payload = CustomerObserver::toPayload($customer);

        $this->assertSame(CustomerObserver::PAYLOAD_FIELDS, array_keys($payload));
        $this->assertArrayNotHasKey('bills', $payload);
        $this->assertArrayNotHasKey('user', $payload);
        $this->assertSame('9690000001', $payload['mobile']);
        $this->assertSame(4, $payload['user_id']);
    }

    public function test_flag_off_does_not_write_or_forget(): void
    {
        $this->cache->shouldReceive('enabled')->times(3)->with('customers')->andReturn(false);
        $this->cache->shouldReceive('put')->never();
        $this->cache->shouldReceive('forget')->never();

        $customer = $this->customer();
        $this->observer->created($customer);
        $this->observer->updated($customer);
        $this->observer->deleted($customer);
    }

    public function test_created_puts_customer_record(): void
    {
        $customer = $this->customer();
        $this->cache->shouldReceive('enabled')->once()->with('customers')->andReturn(true);
        $this->cache->shouldReceive('put')->once()->with(
            CacheKeys::customer(9),
            Mockery::on(fn ($payload) => $payload['name'] === 'QA Customer' && $payload['mobile'] === '9690000001')
        );

        $this->observer->created($customer);
    }

    public function test_updated_refreshes_name_and_mobile(): void
    {
        $customer = $this->customer();
        $customer->name = 'Renamed Customer';
        $customer->mobile = '9690000002';
        $this->cache->shouldReceive('enabled')->once()->with('customers')->andReturn(true);
        $this->cache->shouldReceive('put')->once()->with(
            'customer:9',
            Mockery::on(function ($payload) {
                return $payload['name'] === 'Renamed Customer'
                    && $payload['mobile'] === '9690000002'
                    && array_keys($payload) === CustomerObserver::PAYLOAD_FIELDS;
            })
        );

        $this->observer->updated($customer);
    }

    public function test_deleted_forgets_customer_record(): void
    {
        $this->cache->shouldReceive('enabled')->once()->with('customers')->andReturn(true);
        $this->cache->shouldReceive('forget')->once()->with('customer:9');

        $this->observer->deleted($this->customer());
    }

    public function test_put_failure_does_not_escape_create_or_update(): void
    {
        $customer = $this->customer();
        $customer->email = 'secret@example.test';
        $this->cache->shouldReceive('enabled')->twice()->andReturn(true);
        $this->cache->shouldReceive('put')->twice()->andThrow(new RuntimeException('redis down'));
        $this->logger->shouldReceive('warning')->twice()->with(
            Mockery::type('string'),
            Mockery::on(function (array $context) {
                $encoded = json_encode($context);

                return ($context['domain'] ?? null) === 'customers'
                    && ($context['key'] ?? null) === 'customer:9'
                    && ($context['operation'] ?? null) === 'put'
                    && ($context['exception'] ?? null) === RuntimeException::class
                    && ! str_contains((string) $encoded, 'secret@example.test');
            })
        );

        try {
            $this->observer->created($customer);
            $this->observer->updated($customer);
            $escaped = false;
        } catch (Throwable) {
            $escaped = true;
        }

        $this->assertFalse($escaped);
    }

    public function test_forget_failure_does_not_escape_delete(): void
    {
        $this->cache->shouldReceive('enabled')->once()->andReturn(true);
        $this->cache->shouldReceive('forget')->once()->andThrow(new RuntimeException('redis down'));
        $this->logger->shouldReceive('warning')->once()->with(
            Mockery::type('string'),
            Mockery::on(fn (array $context) => ($context['operation'] ?? null) === 'forget')
        );

        try {
            $this->observer->deleted($this->customer());
            $escaped = false;
        } catch (Throwable) {
            $escaped = true;
        }

        $this->assertFalse($escaped);
    }

    private function customer(): Customer
    {
        $customer = new Customer;
        $customer->setRawAttributes([
            'id' => 9,
            'name' => 'QA Customer',
            'mobile' => '9690000001',
            'email' => 'qa@example.test',
            'notes' => 'note',
            'bullding_no' => '1',
            'street_name' => 'Street',
            'district' => 'District',
            'city' => 'City',
            'postal_code' => '12345',
            'additional_no' => '2',
            'other_buyer_id' => 'OID',
            'vat_registration_number' => '300',
            'user_id' => 4,
            'walkin_customer' => 0,
        ], true);

        return $customer;
    }
}
