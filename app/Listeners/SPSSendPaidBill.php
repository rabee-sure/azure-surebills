<?php

namespace App\Listeners;

use App\Events\BillPaid;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SPSSendPaidBill
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
     * @param  \App\Events\BillPaid  $event
     * @return void
     */
    public function handle(BillPaid $event)
    {
        //Get paid bill transactions data
        $data['total'] = $event->bill->total;
        $data['payment_fees'] = $event->bill->payment_fees;
        $data['payment_fees_vat'] = $event->bill->payment_fees_vat;
        $data['net_amount'] = $data['total'] - ($data['payment_fees'] + $data['payment_fees_vat']);

        //Send data to sps api
    }
}
