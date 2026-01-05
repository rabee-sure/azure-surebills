<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\UserBalance;
use Illuminate\Support\Facades\DB;

class TransactionObserver
{
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
}
