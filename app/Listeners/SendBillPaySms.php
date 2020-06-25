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
                $message = __('هلاً :name<br>لديك فاتورة بقيمة :total SAR من :business_name يمكنك الدفع بكل سهولة الأن<br> :url', ['total' => $event->bill->total, 'business_name' => $event->bill->business_name, 'name' => $event->bill->customer_name, 'url' => $event->bill->pay_url]);
                $message .= PHP_EOL;
                $mobile = (int) $event->bill->customer_mobile;
                $mobile = (int) '966'.$mobile;
                UnifonicFacade::send($mobile, $message);
            }
        }
    }
}
