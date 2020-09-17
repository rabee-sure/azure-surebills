<?php

namespace App\Listeners;

use App\Events\BillCreated;
use App\Mail\SendBillPayLink;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendBillPayEmail implements ShouldQueue
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
            //customer 
            $subject = 'You’ve got a new bill of '. $event->bill->total .' SAR';
            Mail::to($event->bill->customer_email)
                ->send(new SendBillPayLink($event->bill, $subject));
        }
        //owner
        $subject = 'Your bill of '. $event->bill->total.' SAR has been created';
        Mail::to($event->bill->user->email)
            ->send(new SendBillPayLink($event->bill, $subject));
    }
}
