<?php

namespace App\Listeners\Webhook;

use App\Models\Bill;
use App\Models\WebhookLog;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Multicaret\Unifonic\UnifonicFacade;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

class SaveWebhookSucceededLog implements ShouldQueue
{
    use IsMonitored;
    
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
    public function handle(WebhookCallSucceededEvent $event)
    {
        $bill = Bill::find($event->payload['bill_id']??null);

        $log = new WebhookLog;
        $log->status = 1;
        $log->bill_id = $bill->id??null;
        $log->user_id = $bill->user_id ?? $event->payload['account_id'];
        $log->application_id = $bill->application_id??null;
        $log->error_message = $event->errorMessage ?? '';
        $log->status_code = $event->response? $event->response->getStatusCode():0;
        $log->response = $event->response? json_decode($event->response->getBody(), true): [];
        $log->payload = $event->payload;
        $log->save();

    }
}
