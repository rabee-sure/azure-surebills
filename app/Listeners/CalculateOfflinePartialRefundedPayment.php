<?php

namespace App\Listeners;

use App\Events\BillOfflinePartialRefunded;
use App\Jobs\OfflinePartialRefundTransactionsForOwner;
use Illuminate\Contracts\Queue\ShouldQueue;

class CalculateOfflinePartialRefundedPayment implements ShouldQueue
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
    public function handle(BillOfflinePartialRefunded $event)
    {
        $bill = $event->bill;

        if($bill){
            $amount = $event->amount;
                        
            //Offline Partial Refund Transactions For Owner.
            OfflinePartialRefundTransactionsForOwner::dispatch($bill, $amount);
        }
    }
}
