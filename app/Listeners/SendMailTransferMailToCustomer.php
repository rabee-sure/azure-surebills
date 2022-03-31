<?php

namespace App\Listeners;

use App\Events\TransferCreated;
use App\Mail\SendTransferToCustomer;
use Illuminate\Support\Facades\Mail;

class SendMailTransferMailToCustomer
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
     * @param  TransferCreated  $event
     * @return void
     */
    public function handle(TransferCreated $event)
    {
//        $message = (new SendTransferToCustomer($event->transfer))->onQueue(env('EMAILS_QUEUE'));
//        Mail::to($event->transfer->user->email)->queue($message);

    }
}
