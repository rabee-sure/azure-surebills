<?php

namespace App\Observers;

use App\Transaction;
use App\Transfer;

class TransferObserver
{
    /**
     * Handle the transfer "created" event.
     *
     * @param  \App\Transfer  $transfer
     * @return void
     */
    public function created(Transfer $transfer)
    {
        $transaction = new Transaction;
        $transaction->user_id     = $transfer->user_id;
        $transaction->type        = 'debit';
        $transaction->amount      = -$transfer->amount;
        $transaction->reference   = $transfer->id;
        $transaction->receipt     = $transaction->generateReceipt();
        $transaction->description = "id: {$transfer->id} - Transfer Processing";
        $transaction->balance     = $transaction->user->balance - $transaction->amount;
        $transaction->save();
    }

    /**
     * Handle the transfer "updated" event.
     *
     * @param  \App\Transfer  $transfer
     * @return void
     */
    public function updated(Transfer $transfer)
    {
        //
    }

    /**
     * Handle the transfer "deleted" event.
     *
     * @param  \App\Transfer  $transfer
     * @return void
     */
    public function deleted(Transfer $transfer)
    {
        //
    }

    /**
     * Handle the transfer "restored" event.
     *
     * @param  \App\Transfer  $transfer
     * @return void
     */
    public function restored(Transfer $transfer)
    {
        //
    }

    /**
     * Handle the transfer "force deleted" event.
     *
     * @param  \App\Transfer  $transfer
     * @return void
     */
    public function forceDeleted(Transfer $transfer)
    {
        //
    }
}
