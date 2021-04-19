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
        $payment_log = $event->bill->success_payment;

        if($payment_log){
            $bill = $event->bill;
            $amount = $event->amount;
                        
            //Refund Transactions For Owner.
            PartialRefundTransactionsForOwner::dispatch($bill, $amount);

            //Refund Transactions For Channel
            PartialRefundTransactionsForChannel::dispatch($bill, $amount);

            //Refund Transactions For SureBills
            PartialRefundTransactionsForSureBills::dispatch($bill, $amount);

            $this->updateBillAmounts($bill, $amount);
        }
    }

    protected function updateBillAmounts($bill, $amount)
    {
        $bill->total = $bill->total - $amount;
        $percentage = $bill->pricing['fees_percentage'];
        $fixed = $bill->pricing['fees_fixed'];
        $bill->payment_fees = $bill->total * ($percentage / 100) + $fixed;
        $bill->payment_fees_vat = $bill->payment_fees * ( $bill->pricing['vat_percentage']/ 100);
        $payment_surebills = BillService::getPaymentSurebillsFees($bill);
        $bill->payment_surebills_fees = $payment_surebills['fees'];
        $bill->payment_surebills_fees_vat = $payment_surebills['fees_vat'];

        $payment_channel = BillService::getPaymentChannelFees($bill);
        $bill->payment_channel_fees = $payment_channel['fees'];
        $bill->payment_channel_fees_vat = $payment_channel['fees_vat'];
        $bill->save();
    }
}
