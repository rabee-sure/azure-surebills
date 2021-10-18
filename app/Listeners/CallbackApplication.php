<?php

namespace App\Listeners;

use App\Events\BillStatusUpdated;
use App\Jobs\CallbackWebhook;
use Spatie\WebhookServer\WebhookCall;

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
            $log= $bill->last_payment->results['response'];
            WebhookCall::create()
                ->url($bill->application->webhook_url)
                ->payload([
                    'bill_number' => $bill->number,
                    'reference_id' => $bill->reference_id,
                    'status' => $bill->status,
                    'bill_id' => $bill->id,
                    'pay_url' => $bill->pay_url,
                    'total' => $bill->total,
                    
                    'payment_brand' => $log['paymentBrand']??null,
                    'last_4_digits' => $log['card']['last4Digits']??null,
                    'code' => $log['result']['code']??null,
                    'description' => $log['result']['description']??null,

                ])
                ->useSecret($bill->application->webhook_secret)
                ->dispatch()
                ->onQueue(env('WEBHOOK_QUEUE'));
                            CallbackWebhook::dispatch($bill);
        }
    }
}
