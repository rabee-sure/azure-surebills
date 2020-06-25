<?php

namespace App\Listeners;

use App\Events\BillPaid;
use App\Mail\SendBillPaidToOwner;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendBillPaidEmailToOwner
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
        Mail::to($event->bill->user->email)->send(new SendBillPaidToOwner($event->bill));
    }
}
