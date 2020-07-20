<?php

namespace App\Listeners;

use App\Transaction;
use App\Events\BillPaid;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddBillTransactions
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
    public function handle(BillPaid $event)
    {
        $payment = $event->bill->success_payment;

        Transaction::deposit('bill', $payment);
    }
}
