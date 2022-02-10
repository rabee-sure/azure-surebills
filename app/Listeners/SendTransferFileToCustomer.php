<?php

namespace App\Listeners;

use App\Events\TransferFileGenerated;
use App\Mail\SendTransferToCustomer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendTransferFileToCustomer
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
     * @param  \App\Events\TransferFileGenerated  $event
     * @return void
     */
    public function handle(TransferFileGenerated $event)
    {
        $message = (new SendTransferToCustomer($event->transfer))->onQueue(env('EMAILS_QUEUE'));
        Mail::to($event->transfer->user->email)->queue($message);
    }
}
