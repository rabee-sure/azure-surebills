<?php

namespace App\Listeners\Webhook;

use App\Models\Bill;
use App\Models\WebhookLog;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Multicaret\Unifonic\UnifonicFacade;
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
        $bill = Bill::find($event->payload['bill_id']);

        $log = new WebhookLog;
        $log->status = 0;
        $log->bill_id = $bill->id;
        $log->user_id = $bill->user_id;
        $log->application_id = $bill->application_id;
        $log->error_message = $event->errorMessage;
        $log->status_code = $event->response? $event->response->getStatusCode():0;
        $log->response = $event->response? json_decode($event->response->getBody(), true): [];
        $log->payload = $event->payload;
        $log->save();
    }
}
