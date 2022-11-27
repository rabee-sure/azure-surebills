<?php

namespace App\Listeners;

use App\Events\PosBillPaid;
use App\Jobs\MakeOfflineTransactionsForOwner;
use Illuminate\Contracts\Queue\ShouldQueue;

class CalculateOfflinePosPayment implements ShouldQueue
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
    public function handle(PosBillPaid $event)
    {
        $bill = $event->bill;

        if($bill && ($bill->status == 'paid_cash' || $bill->status == 'paid_machine')){
            
            //make Offline Transactions For Owner.
            MakeOfflineTransactionsForOwner::dispatch($bill);
        }
    }
}
