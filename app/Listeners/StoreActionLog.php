<?php

namespace App\Listeners;

use App\Events\AddActionLogEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\ActionLog;

class StoreActionLog implements ShouldQueue
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
     * @param  \App\Events\AddActionLogEvent  $event
     * @return void
     */
    public function handle(AddActionLogEvent $event)
    {
        ActionLog::createActionLog($event->action_name, $event->user_id, $event->payload, $event->modelClass, $event->modelId);
    }
}
