<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\UserBalance;
use Illuminate\Support\Facades\DB;

class TransactionObserver
{
    public function created(Transaction $transaction){
        DB::statement('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
        DB::transaction(function () use ($transaction) {
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
        });
    }
}
