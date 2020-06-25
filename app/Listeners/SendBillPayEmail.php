<?php

namespace App\Listeners;

use App\Events\BillCreated;
use App\Mail\SendBillPayLink;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendBillPayEmail
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
     * @param  BillCreated  $event
     * @return void
     */
    public function handle(BillCreated $event)
    {
        if($event->bill->send_email){
            Mail::to($event->bill->customer_email)->send(new SendBillPayLink($event->bill));
        }
        Mail::to($event->bill->user->email)->send(new SendBillPayLink($event->bill));
    }
}
