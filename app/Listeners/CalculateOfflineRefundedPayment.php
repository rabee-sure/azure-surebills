<?php

namespace App\Listeners;

use App\Events\BillOfflineRefunded;
use App\Jobs\OfflineRefundTransactionsForOwner;
use Illuminate\Contracts\Queue\ShouldQueue;

class CalculateOfflineRefundedPayment implements ShouldQueue
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
    public function handle(BillOfflineRefunded $event)
    {
        $bill = $event->bill;
        $total_remain = $event->total_remain;

        if($bill){
            //Offline Refund Transactions For Owner.
            OfflineRefundTransactionsForOwner::dispatch($bill, $total_remain);
        }
    }
}
