<?php

namespace App\Listeners;

use App\Events\BillPartialRefundedReversed;
use App\Jobs\PartialRefundReverseTransactionsForChannel;
use App\Jobs\PartialRefundReverseTransactionsForOwner;
use App\Jobs\PartialRefundReverseTransactionsForSureBills;

class CalculateReversedPartialRefundedPayment
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
    public function handle(BillPartialRefundedReversed $event)
    {
        $bill = $event->bill;
        $payment_log = $event->payment;

        if($bill && $payment_log){
            $amount = $event->amount;
                        
            //Refund Transactions For Owner.
            PartialRefundReverseTransactionsForOwner::dispatch($bill, $payment_log, $amount);

            // refund fees
            if($bill->user->able_refund_with_fees){
                //Refund Transactions For Channel
                PartialRefundReverseTransactionsForChannel::dispatch($bill, $amount);

                //Refund Transactions For SureBills
                PartialRefundReverseTransactionsForSureBills::dispatch($bill, $amount);
            }

            $this->updateBillAmounts($bill, $amount);
        }
    }

    protected function updateBillAmounts($bill, $amount)
    {
        $amount_prc = $amount/$bill->fixed_total;

        $bill->total = $bill->total + $amount;

        // refund fees
        if($bill->user->able_refund_with_fees){
            $bill->payment_fees += $bill->payment_fees * $amount_prc;
            $bill->payment_fees_vat += $bill->payment_fees_vat * $amount_prc;
            
            $bill->payment_surebills_fees += $bill->payment_surebills_fees * $amount_prc;
            $bill->payment_surebills_fees_vat += $bill->payment_surebills_fees_vat * $amount_prc;

            $bill->payment_channel_fees += $bill->payment_channel_fees * $amount_prc;
            $bill->payment_channel_fees_vat += $bill->payment_channel_fees_vat * $amount_prc;
        }

        $bill->save();
    }
}
