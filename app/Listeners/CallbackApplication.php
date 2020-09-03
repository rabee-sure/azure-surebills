<?php

namespace App\Listeners;

use App\Events\BillStatusUpdated;
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
            if(in_array($bill->status, ['expired'])){
                $url = '?reference_id='.$bill->reference_id.'&status=expired&bill_id='.$bill->id.'&pay_url='.$bill->pay_url;

                $client = new Client(['base_uri' => $bill->application->redirect]);
                $response = $client->request('GET', $url);
                // dd($response);
            }
        }
    }
}
