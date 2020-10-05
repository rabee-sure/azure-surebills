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
        \App::setLocale($event->bill->user->settings->default_lang); 
        if($event->bill->send_email){
            if(isset($event->bill->customer_email)){
                $subject = __('You’ve got a new bill of :total SAR', ['total' => $event->bill->total]);
                Mail::to($event->bill->customer_email)
                    ->send(new SendBillPayLink($event->bill, $subject));
            }
        }

        //owner
        if(isset($event->bill->user->email)){
            $subject = __('Your bill of :total SAR has been created', ['total' => $event->bill->total]);
            Mail::to($event->bill->user->email)
                ->send(new SendBillPayLink($event->bill, $subject));
        }
    }
}
