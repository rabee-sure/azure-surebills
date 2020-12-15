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
        $bankCode   = $transfer->user->bank ? $transfer->user->bank->code : '-';
        $bankNumber = substr($transfer->user->iban_number, -4);

        $transaction = new Transaction;
        $transaction->user_id     = $transfer->user_id;
        $transaction->type        = 'debit';
        $transaction->amount      = $transfer->amount;
        $transaction->reference   = $transfer->id;
        $transaction->description = 'Transfer - ' . $bankCode . ' XXXX' . $bankNumber;
        $transaction->transaction_source = 'transfer';
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
