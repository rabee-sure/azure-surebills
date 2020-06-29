<?php

namespace App\Listeners;

use App\Events\BillCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Multicaret\Unifonic\UnifonicFacade;

class SendBillPaySms
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
        if($event->bill->send_sms){
            $message = __('Hello :name, You’ve got a new bill of :total SAR, From :business_name, Pay now :url', [
                'total' => round($event->bill->total, 2), 
                'business_name' => $event->bill->business_name, 
                'name' => $event->bill->customer_name, 
                'url' => $event->bill->pay_url
            ]);
            if(app()->environment('production')){

                $mobile = (int) $event->bill->customer_mobile;
                $mobile = (int) '966'.$mobile;
                UnifonicFacade::send($mobile, $message);
            }
        }
    }
}
