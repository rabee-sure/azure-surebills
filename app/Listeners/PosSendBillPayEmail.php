<?php

namespace App\Listeners;

use App\Events\PosSendBill;
use App\Mail\SendBillPayLink;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class PosSendBillPayEmail
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
    public function handle(PosSendBill $event)
    {
        \App::setLocale($event->bill->user->settings->default_lang); 
        if(isset($event->bill->customer_email)){
            $subject = __('You’ve got a new bill of :total SAR', ['total' => $event->bill->total]);
            $message = (new SendBillPayLink($event->bill, $subject))->onQueue(env('EMAILS_QUEUE'));
            Mail::to($event->bill->customer_email)->queue($message);
        }
    }
}
