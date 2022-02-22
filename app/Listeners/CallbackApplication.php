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
        $payment = $event->payment;

        if($bill->application){
            WebhookCall::create()
                ->url($bill->application->webhook_url)
                ->payload([
                    'bill_number' => $bill->number,
                    'reference_id' => $bill->reference_id,
                    'status' => $bill->status,
                    'bill_id' => $bill->id,
                    'pay_url' => $bill->pay_url,
                    'total' => $bill->total,
                    
                    'code' => '',
                    'payment_brand' => $payment ? $payment->brand : null,
                    'card_number' => $payment ? $payment->brand : null,
                    'last_4_digits' => $payment ? substr($payment->brand, -4) : null,
                    'bank_transaction_id' => $payment ? $payment->bank_transaction_id : null,
                    'description' => $payment ? $payment->bank_message : null,

                ])
                ->useSecret($bill->application->webhook_secret)
                ->dispatch()
                ->onQueue(env('WEBHOOK_QUEUE'));
                            CallbackWebhook::dispatch($bill);
        }
    }
}
