<?php

namespace App\Listeners\Webhook;

use App\Models\Bill;
use App\Models\WebhookLog;
use GuzzleHttp\Psr7\Response;
use Multicaret\Unifonic\UnifonicFacade;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;

class SaveWebhookFailedLog implements ShouldQueue
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
    public function handle(WebhookCallFailedEvent $event)
    {
        $bill = Bill::find($event->payload['bill_id']??null);

        $log = new WebhookLog;
        $log->status = 0;
        $log->bill_id = $bill->id ?? null;
        $log->user_id = $bill->user_id ?? $event->payload['account_id'];
        $log->application_id = $bill->application_id ?? null;
        $log->error_message = $event->errorMessage;
        $log->status_code = $event->response? $event->response->getStatusCode():0;
        $log->response = $event->response? json_decode($event->response->getBody(), true): [];
        $log->payload = $event->payload;
        $log->save();
    }
}
