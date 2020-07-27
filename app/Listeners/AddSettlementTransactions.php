<?php

namespace App\Listeners;

use App\Transaction;
use App\Events\SettlementCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddSettlementTransactions
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
    public function handle(SettlementCreated $event)
    {
        $settlement =  $event->settlement;

        $transaction = new  Transaction;
        $transaction->user_id     = $settlement->user_id;
        $transaction->type        = 'debit';
        $transaction->amount      = $settlement->amount;
        $transaction->reference   = $settlement->id;
        $transaction->receipt   = $transaction->generateReceipt();
        $transaction->description = 'Settlement - Transaction Processing';
        $transaction->balance     = $transaction->user->balance - $transaction->amount;
        $transaction->save();
    }
}
