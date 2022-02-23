<?php

namespace App\Listeners;

use App\Events\BillPartialRefunded;
use App\Jobs\PartialRefundTransactionsForChannel;
use App\Jobs\PartialRefundTransactionsForOwner;
use App\Jobs\PartialRefundTransactionsForSureBills;
use App\Models\Bill;
use App\Models\Transaction;
use App\Services\BillService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CalculatePartialRefundedPayment
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
    public function handle(BillPartialRefunded $event)
    {
        $bill = $event->bill;
        $payment_log = $event->payment;

        if($bill && $payment_log){
            $amount = $event->amount;
                        
            //Refund Transactions For Owner.
            PartialRefundTransactionsForOwner::dispatch($bill, $payment_log, $amount);

            // refund fees
            if($bill->user->able_refund_with_fees){
                //Refund Transactions For Channel
                PartialRefundTransactionsForChannel::dispatch($bill, $amount);

                //Refund Transactions For SureBills
                PartialRefundTransactionsForSureBills::dispatch($bill, $amount);
            }

            $this->updateBillAmounts($bill, $amount);
        }
    }

    protected function updateBillAmounts($bill, $amount)
    {
        $amount_prc = $amount/$bill->total;

        $bill->total = $bill->total - $amount;

        // refund fees
        if($bill->user->able_refund_with_fees){
            $bill->payment_fees -= $bill->payment_fees * $amount_prc;
            $bill->payment_fees_vat -= $bill->payment_fees_vat * $amount_prc;
            
            $bill->payment_surebills_fees -= $bill->payment_surebills_fees * $amount_prc;
            $bill->payment_surebills_fees_vat -= $bill->payment_surebills_fees_vat * $amount_prc;

            $bill->payment_channel_fees -= $bill->payment_channel_fees * $amount_prc;
            $bill->payment_channel_fees_vat -= $bill->payment_channel_fees_vat * $amount_prc;
        }

        $bill->save();
    }
}
