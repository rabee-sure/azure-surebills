<?php

namespace App\Listeners;

use App\PaymentLog;
use App\Transaction;
use App\Events\BillPaid;
use Illuminate\Support\Facades\Log;
use App\Mail\SendBillPaidToCustomer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
            $bill->pricing_fees_details = $percentage.'%,'. $fixed;
            $bill->payment_fees = $bill->total * ($percentage / 100) + $fixed;
            $bill->payment_fees_vat = $bill->payment_fees * (Transaction::VAT_PERCENTAGE / 100);
            $bill->save();
        }

    }
}
