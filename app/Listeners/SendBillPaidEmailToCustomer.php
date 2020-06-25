<?php

namespace App\Listeners;

use App\Events\BillPaid;
use App\Mail\SendBillPaidToCustomer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendBillPaidEmailToCustomer
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
        Mail::to($event->bill->customer_email)->send(new SendBillPaidToCustomer($event->bill));

    }
}
