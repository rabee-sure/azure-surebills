<?php

namespace App\Listeners;

use App\Events\BillCreated;
use App\Services\SMSService;
use Multicaret\Unifonic\UnifonicFacade;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class SendBillPaySms implements ShouldQueue
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
    public function handle(BillCreated $event)
    {
        // \App::setLocale($event->bill->user->settings->default_lang);

        if($event->bill->send_sms && ($event->bill->application_id == null || $event->bill->application->channel_id == null)){
            $message = __('Hello :name, You’ve got a new bill of :total SAR, From :business_name, Pay now :url', [
                'total' => round($event->bill->total, 2),
                'business_name' => $event->bill->user->business_name,
                'name' => $event->bill->customer_name,
                'url' => $event->bill->pay_url
            ]);
            if(app()->environment('production'))
            {
                $mobile = (int) $event->bill->customer_mobile;
                
                $smsService = new SMSService();

                $response = $smsService->sendSMS($mobile, $message);
            }
        }
    }
}
