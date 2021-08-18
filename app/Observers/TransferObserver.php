<?php

namespace App\Observers;

use App\Events\TransferCompleted;
use App\Models\Transaction;
use App\Models\Transfer;

class TransferObserver
{
    /**
     * Handle the transfer "created" event.
     *
     * @param  \App\Models\Transfer  $transfer
     * @return void
     */
    public function created(Transfer $transfer)
    {
        if($transfer->status == "completed"){
            TransferCompleted::dispatch($transfer);
        }
    }

    /**
     * Handle the transfer "updated" event.
     *
     * @param  \App\Models\Transfer  $transfer
     * @return void
     */
    public function updated(Transfer $transfer)
    {
        if($transfer->isDirty('status') && $transfer->status == "completed"){
            TransferCompleted::dispatch($transfer);
        }
    }

    /**
     * Handle the transfer "deleted" event.
     *
     * @param  \App\Models\Transfer  $transfer
     * @return void
     */
    public function deleted(Transfer $transfer)
    {
        //
    }

    /**
     * Handle the transfer "restored" event.
     *
     * @param  \App\Models\Transfer  $transfer
     * @return void
     */
    public function restored(Transfer $transfer)
    {
        //
    }

    /**
     * Handle the transfer "force deleted" event.
     *
     * @param  \App\Models\Transfer  $transfer
     * @return void
     */
    public function forceDeleted(Transfer $transfer)
    {
        //
    }
}
