<?php

namespace App\Listeners;

use App\Events\BillStatusUpdated;
use App\Jobs\CallbackWebhook;
use Spatie\WebhookServer\WebhookCall;

class CallbackApplication
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
     * @param  BillStatusUpdated  $event
     * @return void
     */
    public function handle(BillStatusUpdated $event)
    {
        $bill = $event->bill;

        if($bill->application){
            WebhookCall::create()
                ->url($bill->application->webhook_url)
                ->payload([
                    'reference_id' => $bill->reference_id,
                    'status' => $bill->status,
                    'bill_id' => $bill->id,
                    'pay_url' => $bill->pay_url,
                    'total' => $bill->total,
                ])
                ->useSecret($bill->application->webhook_secret)
                ->dispatch()
                ->onQueue(env('WEBHOOK_QUEUE'));
    
            CallbackWebhook::dispatch($bill);
        }
    }
}
