<?php

namespace App\Observers;

use App\Models\Customer;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Record cache for customer:{id} (PR-12).
 *
 * Display/read cache only. update() and destroy() must keep using a MySQL Customer.
 * Query-builder deletes, including DeleteUncompletedUsers, do not fire this observer.
 */
class CustomerObserver
{
    /**
     * Fields required by the edit form and CustomerPolicy ownership check.
     *
     * @var list<string>
     */
    public const PAYLOAD_FIELDS = [
        'id',
        'name',
        'mobile',
        'email',
        'notes',
        'bullding_no',
        'street_name',
        'district',
        'city',
        'postal_code',
        'additional_no',
        'other_buyer_id',
        'vat_registration_number',
        'user_id',
    ];

    public function __construct(
        private readonly SureBillsCache $cache,
    ) {}

    public function created(Customer $customer): void
    {
        $this->put($customer);
    }

    public function updated(Customer $customer): void
    {
        $this->put($customer);
    }

    public function deleted(Customer $customer): void
    {
        $this->forget($customer);
    }

    /**
     * @return array<string, mixed>
     */
    public static function toPayload(Customer $customer): array
    {
        $payload = [];

        foreach (self::PAYLOAD_FIELDS as $field) {
            $payload[$field] = $customer->getAttribute($field);
        }

        return $payload;
    }

    public static function isValidPayload(mixed $payload, int $id): bool
    {
        if (! is_array($payload)) {
            return false;
        }

        foreach (self::PAYLOAD_FIELDS as $field) {
            if (! array_key_exists($field, $payload)) {
                return false;
            }
        }

        return count($payload) === count(self::PAYLOAD_FIELDS)
            && (int) $payload['id'] === $id;
    }

    /**
     * Read-only Customer for edit(). Do not save() this instance.
     *
     * @param  array<string, mixed>  $payload
     */
    public static function modelFromPayload(array $payload): Customer
    {
        $attributes = [];

        foreach (self::PAYLOAD_FIELDS as $field) {
            $attributes[$field] = $payload[$field] ?? null;
        }

        $customer = new Customer;
        $customer->setRawAttributes($attributes, true);
        $customer->exists = true;

        return $customer;
    }

    private function put(Customer $customer): void
    {
        if (! $this->cache->enabled('customers')) {
            return;
        }

        try {
            $this->cache->put(
                CacheKeys::customer($customer->id),
                self::toPayload($customer)
            );
        } catch (Throwable $e) {
            Log::warning('customers cache rewrite failed after customer mutation', [
                'domain' => 'customers',
                'key' => CacheKeys::customer($customer->id),
                'operation' => 'put',
                'exception' => $e::class,
            ]);
        }
    }

    private function forget(Customer $customer): void
    {
        if (! $this->cache->enabled('customers')) {
            return;
        }

        try {
            $this->cache->forget(CacheKeys::customer($customer->id));
        } catch (Throwable $e) {
            Log::warning('customers cache forget failed after customer delete', [
                'domain' => 'customers',
                'key' => CacheKeys::customer($customer->id),
                'operation' => 'forget',
                'exception' => $e::class,
            ]);
        }
    }
}
