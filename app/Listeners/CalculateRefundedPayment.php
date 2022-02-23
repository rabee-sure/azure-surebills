<?php

namespace App\Listeners;

use App\Models\Bill;
use App\Events\BillRefunded;
use App\Jobs\RefundTransactionsForChannel;
use App\Jobs\RefundTransactionsForOwner;
use App\Jobs\RefundTransactionsForSureBills;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CalculateRefundedPayment
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
    public function handle(BillRefunded $event)
    {
        $bill = $event->bill;
        $payment_log = $event->payment;

        if($bill && $payment_log){
            //Refund Transactions For Owner.
            RefundTransactionsForOwner::dispatch($bill, $payment_log);

            // refund fees
            if($bill->user->able_refund_with_fees){
                //Refund Transactions For Channel
                RefundTransactionsForChannel::dispatch($bill, $payment_log);

                //Refund Transactions For SureBills
                RefundTransactionsForSureBills::dispatch($bill, $payment_log);
            }

            // refund fees
            if($bill->user->able_refund_with_fees){
                $bill->payment_fees = null;
                $bill->payment_fees_vat = null;
                $bill->payment_channel_fees = null;
                $bill->payment_channel_fees_vat = null;
                $bill->payment_surebills_fees = null;
                $bill->payment_surebills_fees_vat = null;
            }
            $bill->save();
        }
    }
}
