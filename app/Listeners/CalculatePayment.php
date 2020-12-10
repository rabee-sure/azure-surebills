<?php

namespace App\Listeners;

use App\Bill;
use App\Events\BillPaid;
use App\Jobs\MakeTransactionsForChannel;
use App\Jobs\MakeTransactionsForOwner;
use App\Jobs\MakeTransactionsForSureBills;
use App\Mail\SendBillPaidToCustomer;
use App\PaymentLog;
use App\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CalculatePayment
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
        $payment_log = $event->bill->success_payment;

        if($payment_log){
            $bill = $event->bill;
            $percentage = $bill->getPercentage($payment_log);
            $fixed = $bill->getFixed($payment_log);

            $bill->settled = false;
            $bill->pricing_fees_details = $percentage.'%,'. $fixed;
            $bill->payment_fees = $bill->total * ($percentage / 100) + $fixed;
            $bill->payment_fees_vat = $bill->payment_fees * (Transaction::VAT_PERCENTAGE / 100);
            $bill->save();
            
            //make Transactions For Owner.
            MakeTransactionsForOwner::dispatch($bill, $payment_log);

            //make Transactions For Channel
            MakeTransactionsForChannel::dispatch($bill, $payment_log);

            //make Transactions For SureBills
            MakeTransactionsForSureBills::dispatch($bill, $payment_log);
        }
    }
}
