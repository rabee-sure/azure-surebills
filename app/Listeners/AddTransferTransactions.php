<?php

namespace App\Listeners;

use App\Transaction;
use App\Events\TransferCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddTransferTransactions
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  BillPaid  $event
     * @return void
     */
    public function handle(TransferCreated $event)
    {
        $Transfer =  $event->Transfer;

        $transaction = new  Transaction;
        $transaction->user_id     = $Transfer->user_id;
        $transaction->type        = 'debit';
        $transaction->amount      = $Transfer->amount;
        $transaction->reference   = $Transfer->id;
        $transaction->receipt   = $transaction->generateReceipt();
        $transaction->description = 'Transfer - Transaction Processing';
        $transaction->balance     = $transaction->user->balance - $transaction->amount;
        $transaction->save();
    }
}
