<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\UserBalance;
use App\Services\Cache\CacheKeys;
use App\Services\Cache\SureBillsCache;
use Illuminate\Support\Facades\DB;
use Throwable;

class TransactionObserver
{
    /** @var SureBillsCache */
    private $cache;

    public function __construct(SureBillsCache $cache)
    {
        $this->cache = $cache;
    }

    public function created(Transaction $transaction){
        // Execute the balance update logic
        $updateBalance = function () use ($transaction) {
            $user_balance = UserBalance::where('user_id', $transaction->user_id)->lockForUpdate()->first();
            if(!$user_balance){
                $user_balance = new UserBalance();
                $user_balance->user_id = $transaction->user_id;
                if($transaction->type == 'debit'){
                    $user_balance->balance = $transaction->user->balance - $transaction->amount;
                }else{
                    $user_balance->balance = $transaction->user->balance + $transaction->amount;
                }
            }else{
                if($transaction->type == 'debit'){
                    $user_balance->balance -= $transaction->amount;
                }else{
                    $user_balance->balance += $transaction->amount;
                }
            }

            $user_balance->save();
            $this->scheduleHomeBalanceForget($transaction->user_id);
        };

        // Only set isolation level if not already in a transaction
        // Note: In MySQL/MariaDB, isolation level can only be set before a transaction starts
        if (DB::transactionLevel() === 0) {
            DB::statement('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
            DB::transaction($updateBalance);
        } else {
            // Already in a transaction, just execute the logic
            // Laravel's DB::transaction() will use savepoints for nested transactions
            DB::transaction($updateBalance);
        }
    }

    /**
     * Forget the Home display balance after the balance write commits.
     *
     * @param  int|string  $userId
     */
    private function scheduleHomeBalanceForget($userId): void
    {
        try {
            if (! $this->cache->enabled('merchant_home')) {
                return;
            }

            DB::afterCommit(function () use ($userId) {
                try {
                    $this->cache->forget(CacheKeys::uiBalance($userId));
                } catch (Throwable $e) {
                    // A Redis failure must not fail the committed transaction.
                }
            });
        } catch (Throwable $e) {
            // Scheduling the forget must not roll back the balance write.
        }
    }
}
