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
            if(app()->environment('production')){
                $message = __('to pay bill please open link below: ',[],'en') . $event->bill->pay_url;
                $message .= PHP_EOL;
                $mobile = (int) $this->mobile;
                $mobile = (int) '966'.$this->mobile;
                UnifonicFacade::send($event->bill->customer_mobile, $message);
            }
        }
    }
}
