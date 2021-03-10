<?php

namespace App\Listeners;

use App\Events\BillPaid;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Spatie\WebhookServer\WebhookCall;

class SendBillPaidWebhook
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
        if($event->bill->client_id && $event->bill->client->webhook_url){
            WebhookCall::create()
               ->url($event->bill->client->webhook_url)
               ->payload([
                    'type' => 'BillPaid',
                    'bill' => $event->bill,
               ])
               ->useSecret($event->bill->client->webhook_secret)
               ->dispatch()
               ->onQueue('webhook');          
        }

    }
}
