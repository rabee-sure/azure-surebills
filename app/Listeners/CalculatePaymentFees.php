<?php

namespace App\Listeners;

use App\Events\BillPaid;
use App\Mail\SendBillPaidToCustomer;
use App\PaymentLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CalculatePaymentFees
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
     * @param  BillPaid  $event
     * @return void
     */
    public function handle(BillPaid $event)
    {
        $bill = $event->bill;
        $user = $event->bill->user;
        $log = $event->bill->success_payment;

        if($log){
            $percentage = (isset($log->results['response']['paymentBrand'])  && $log->results['response']['paymentBrand'] == 'MADA') ? $user->mada_percentage : $user->credit_cards_percentage;
            $fixed = (isset($log->results['response']['paymentBrand']) && $log->results['response']['paymentBrand'] == 'MADA') ? $user->mada_fixed : $user->credit_cards_fixed;

            $bill->settled = false;
            $bill->payment_fees = $bill->total * ($percentage / 100) + $fixed;
            $bill->save();
        }

    }
}
