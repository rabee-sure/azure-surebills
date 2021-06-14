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
        if($event->bill->user->settings->paid_send_email && isset($event->bill->customer_email)){
            $message = (new SendBillPaidToCustomer($event->bill))->onQueue(env('EMAILS_QUEUE'));
            Mail::to($event->bill->customer_email)->queue($message);
        }

    }
}
