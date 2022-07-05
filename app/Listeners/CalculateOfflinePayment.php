<?php

namespace App\Listeners;

use App\Events\BillStatusUpdated;
use App\Jobs\MakeOfflineTransactionsForOwner;

class CalculateOfflinePayment
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
    public function handle(BillStatusUpdated $event)
    {
        $bill = $event->bill;

        if($bill && ($bill->status == 'paid_cash' || $bill->status == 'paid_bank_transfer')){
            
            //make Offline Transactions For Owner.
            MakeOfflineTransactionsForOwner::dispatch($bill);
        }
    }
}
