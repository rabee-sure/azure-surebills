<?php

namespace App\Observers;

use App\Events\AddActionLogEvent;
use App\Models\Bank;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class BankObserver
{
    public function __construct(
        private readonly SureBillsCache $cache,
    ) {}

    /**
     * Handle the Bank "created" event.
     *
     * @param  \App\Models\Bank  $bank
     * @return void
     */
    public function created(Bank $bank)
    {
        if (Auth::guard('admins')->check()) {
            event(new AddActionLogEvent(
                'create_bank',
                Auth::id(),
                [
                    'message' => [
                        'adminname' => Auth::user()->name,
                        'time' => $bank->created_at,
                    ],
                    'changes' => [],
                ],
                $bank->id,
                Bank::class
            ));
        }

        $this->putBankRecordAndBump($bank);
    }

    /**
     * Handle the Bank "updated" event.
     *
     * @param  \App\Models\Bank  $bank
     * @return void
     */
    public function updated(Bank $bank)
    {
        if (Auth::guard('admins')->check()) {
            $fields = config('bankfields');

            $fieldsChanges = [];
            foreach ($fields as $field) {
                if ($bank->isDirty($field)) {
                    $fieldsChanges[$field] = [
                        'old_value' => $bank->getOriginal($field),
                        'new_value' => $bank->$field,
                    ];
                }
            }
            event(new AddActionLogEvent(
                'update_bank',
                Auth::id(),
                [
                    'message' => [
                        'adminname' => Auth::user()->name,
                        'time' => $bank->updated_at,
                    ],
                    'changes' => $fieldsChanges,
                ],
                $bank->id,
                Bank::class
            ));
        }

        $this->putBankRecordAndBump($bank);
    }

    /**
     * Handle the Bank "deleted" event.
     *
     * @param  \App\Models\Bank  $bank
     * @return void
     */
    public function deleted(Bank $bank)
    {
        if (Auth::guard('admins')->check()) {
            event(new AddActionLogEvent(
                'delete_bank',
                Auth::id(),
                [
                    'message' => [
                        'adminname' => Auth::user()->name,
                        'time' => $bank->created_at,
                    ],
                    'changes' => [],
                ],
                $bank->id,
                Bank::class
            ));
        }

        $this->forgetBankRecordAndBump($bank);
    }

    /**
     * Handle the Bank "restored" event.
     *
     * @param  \App\Models\Bank  $bank
     * @return void
     */
    public function restored(Bank $bank)
    {
        //
    }

    /**
     * Handle the Bank "force deleted" event.
     *
     * @param  \App\Models\Bank  $bank
     * @return void
     */
    public function forceDeleted(Bank $bank)
    {
        //
    }

    /**
     * Shared bank record payload (PR-07 / PR-08 contract).
     *
     * @return array<string, mixed>
     */
    public static function recordPayload(Bank $bank): array
    {
        return [
            'id' => $bank->id,
            'code' => $bank->code,
            'name' => $bank->getTranslations('name'),
            'active' => $bank->active,
            'sort_number' => $bank->sort_number,
            'fees' => $bank->fees,
        ];
    }

    private function putBankRecordAndBump(Bank $bank): void
    {
        if (! $this->cache->enabled('banks')) {
            return;
        }

        $key = CacheKeys::bank($bank->id);

        try {
            $this->cache->put($key, self::recordPayload($bank));
            $this->cache->bumpVersion(CacheKeys::banksActiveVersion());
        } catch (Throwable $e) {
            Log::warning('banks cache rewrite failed after bank mutation', [
                'domain' => 'banks',
                'key' => $key,
                'operation' => 'put_and_bump',
                'reason' => 'exception',
                'exception' => $e::class,
            ]);
        }
    }

    private function forgetBankRecordAndBump(Bank $bank): void
    {
        if (! $this->cache->enabled('banks')) {
            return;
        }

        $key = CacheKeys::bank($bank->id);

        try {
            $this->cache->forget($key);
            $this->cache->bumpVersion(CacheKeys::banksActiveVersion());
        } catch (Throwable $e) {
            Log::warning('banks cache forget failed after bank delete', [
                'domain' => 'banks',
                'key' => $key,
                'operation' => 'forget_and_bump',
                'reason' => 'exception',
                'exception' => $e::class,
            ]);
        }
    }
}
