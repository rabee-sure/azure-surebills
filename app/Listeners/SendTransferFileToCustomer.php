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
        $emails = explode(",", $event->transfer_emails);
        if(count($emails)){
            foreach ($emails as $email) {
                Mail::to($email)->send(new RequestTransferMail($event->cycleDate, auth()->user(), $event->transfer));
            }
        }
    }
}
