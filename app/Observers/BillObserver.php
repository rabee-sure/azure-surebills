<?php

namespace App\Observers;

use App\Models\Bill;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use DateTimeInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class BillObserver
{
    public function __construct(
        private readonly SureBillsCache $cache,
    ) {}

    public function creating(Bill $bill)
    {
        // generate QR code for pay url 
        $qr_code = generateUrlQRcode($bill->pay_url);
        $bill->qr_code = $qr_code;
    }

    /**
     * Handle the Bill "created" event.
     *
     * @param  \App\Models\Bill  $bill
     * @return void
     */
    public function created(Bill $bill)
    {
        $this->syncPendingBill($bill);
    }

    /**
     * Handle the Bill "updating" event.
     *
     * @param  \App\Models\Bill  $bill
     * @return void
     */
    public function updating(Bill $bill)
    {
        if($bill->isDirty('status')){
            switch ($bill->status) {
                case 'paid':
                    $bill->payment_way = 'online';
                    break;
            
                case 'paid_cash':
                    $bill->payment_way = 'cash';
                    break;

                case 'paid_bank_transfer':
                    $bill->payment_way = 'bank_transfer';
                    break;
                
                case 'paid_machine':
                    $bill->payment_way = 'payment_machine';
                    break;
                
                default:
                    # code...
                    break;
            }
        }
    }

    /**
     * Handle the Bill "updated" event.
     *
     * @param  \App\Models\Bill  $bill
     * @return void
     */
    public function updated(Bill $bill)
    {
        $this->syncPendingBill($bill);
    }

    /**
     * Handle the Bill "deleted" event.
     *
     * @param  \App\Models\Bill  $bill
     * @return void
     */
    public function deleted(Bill $bill)
    {
        //
    }

    /**
     * Handle the Bill "restored" event.
     *
     * @param  \App\Models\Bill  $bill
     * @return void
     */
    public function restored(Bill $bill)
    {
        //
    }

    /**
     * Handle the Bill "force deleted" event.
     *
     * @param  \App\Models\Bill  $bill
     * @return void
     */
    public function forceDeleted(Bill $bill)
    {
        //
    }

    /**
     * Display map only. Payment eligibility stays on the MySQL bill.
     */
    private function syncPendingBill(Bill $bill): void
    {
        if (! $this->cache->enabled('pending_bills')) {
            return;
        }

        $key = CacheKeys::pendingBills($bill->user_id);
        $operation = 'get';

        try {
            $stored = Cache::get($key);
            $map = $this->pendingMap($stored);
            $id = (string) $bill->getAttribute('id');
            $memberExisted = array_key_exists($id, $map);

            if ($bill->status === 'pending') {
                $map[$id] = $this->pendingPayload($bill);
                $operation = 'put';
                $this->cache->put($key, $map, config('surebills_cache.ttl.pending_bills'));

                return;
            }

            if ($stored !== null && ! is_array($stored)) {
                $operation = 'forget';
                $this->cache->forget($key);

                return;
            }

            if (! $memberExisted) {
                return;
            }

            unset($map[$id]);

            if ($map === []) {
                $operation = 'forget';
                $this->cache->forget($key);

                return;
            }

            $operation = 'put';
            $this->cache->put($key, $map, config('surebills_cache.ttl.pending_bills'));
        } catch (Throwable $e) {
            Log::warning('pending bills cache sync failed after bill mutation', [
                'domain' => 'pending_bills',
                'key' => $key,
                'operation' => $operation,
                'exception' => $e::class,
            ]);
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function pendingMap(mixed $stored): array
    {
        if (! is_array($stored)) {
            return [];
        }

        $map = [];

        foreach ($stored as $memberId => $member) {
            if (! is_array($member) || ! $this->isPendingMember($member, (string) $memberId)) {
                continue;
            }

            $map[(string) $memberId] = [
                'id' => $member['id'],
                'amount' => $member['amount'],
                'customer_name' => $member['customer_name'],
                'created_at' => $member['created_at'],
                'status' => $member['status'],
            ];
        }

        return $map;
    }

    /**
     * @param  array<string, mixed>  $member
     */
    private function isPendingMember(array $member, string $memberId): bool
    {
        foreach (['id', 'amount', 'customer_name', 'created_at', 'status'] as $field) {
            if (! array_key_exists($field, $member)) {
                return false;
            }
        }

        return (string) $member['id'] === $memberId
            && $member['status'] === 'pending';
    }

    /**
     * @return array<string, mixed>
     */
    private function pendingPayload(Bill $bill): array
    {
        $createdAt = $bill->getAttribute('created_at');

        return [
            'id' => $bill->getAttribute('id'),
            'amount' => $bill->getAttribute('total'),
            'customer_name' => $bill->getAttribute('customer_name'),
            'created_at' => $createdAt instanceof DateTimeInterface
                ? $createdAt->format('Y-m-d H:i:s')
                : $createdAt,
            'status' => $bill->getAttribute('status'),
        ];
    }
}
