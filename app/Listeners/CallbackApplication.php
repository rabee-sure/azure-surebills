<?php

namespace App\Listeners;

use App\Events\BillStatusUpdated;
use App\Jobs\CallbackWebhook;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Multicaret\Unifonic\UnifonicFacade;

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
            CallbackWebhook::dispatch($bill);
        }
    }
}
