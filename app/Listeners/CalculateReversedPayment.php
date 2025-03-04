<?php

namespace App\Listeners;

use App\Events\BillPaid;
use App\Events\BillPaidReversed;
use App\Jobs\MakeReverseTransactionsForChannel;
use App\Jobs\MakeReverseTransactionsForChannelExtraFees;
use App\Jobs\MakeReverseTransactionsForOwner;
use App\Jobs\MakeReverseTransactionsForSureBills;

class CalculateReversedPayment
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
    public function handle(BillPaidReversed $event)
    {
        $bill = $event->bill;
        $payment_log = $event->payment;

        if($bill && $payment_log){
            //make Transactions For Owner.
            MakeReverseTransactionsForOwner::dispatch($bill, $payment_log);

            //make Transactions For Channel
            MakeReverseTransactionsForChannel::dispatch($bill, $payment_log);

            //make Transactions For SureBills
            MakeReverseTransactionsForSureBills::dispatch($bill, $payment_log);
            
            //make Transactions For Channel Extra Fees
            if($bill->channel_extra_amount){
                MakeReverseTransactionsForChannelExtraFees::dispatch($bill, $payment_log);
            }

            //Send Transaction to SPS
            // SendTransactionToSPS::dispatch($bill, $payment_log);

            $bill->payment_fees = null;
            $bill->payment_fees_vat = null;
            $bill->payment_channel_fees = null;
            $bill->payment_channel_fees_vat = null;
            $bill->payment_surebills_fees = null;
            $bill->payment_surebills_fees_vat = null;
            $bill->save();
        }
    }
}
