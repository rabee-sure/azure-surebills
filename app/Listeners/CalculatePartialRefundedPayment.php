<?php

namespace App\Listeners;

use App\Events\BillPartialRefunded;
use App\Jobs\RefundTransactionsForChannel;
use App\Jobs\RefundTransactionsForOwner;
use App\Jobs\RefundTransactionsForSureBills;
use App\Models\Bill;
use App\Models\Transaction;
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
            $bill->total = $bill->total -$amount;

            //withdrawBillFees
            $transaction = new Transaction;
            $transaction->user_id     = $bill->user_id;
            $transaction->bill_id     = $bill->id;
            $transaction->type        = 'debit';
            $transaction->amount      = $amount;
            $transaction->reference   = $bill->number;
            $transaction->description = 'Partial Refund';
            $transaction->transaction_source = 'refund';
            $transaction->save();

            $percentage = $bill->getPercentage($payment_log);
            $fixed = $bill->getFixed($payment_log);
            $bill->payment_fees = $bill->total * ($percentage / 100) + $fixed;
            $bill->payment_fees_vat = $bill->payment_fees * (Transaction::VAT_PERCENTAGE / 100);

            $payment_surebills = $this->paymentSurebillsFees($bill, $payment_log);
            $bill->payment_surebills_fees = $payment_surebills['fees'];
            $bill->payment_surebills_fees_vat = $payment_surebills['fees_vat'];

            $payment_channel = $this->paymentChannelFees($bill, $payment_log);
            $bill->payment_channel_fees = $payment_channel['fees'];
            $bill->payment_channel_fees_vat = $payment_channel['fees_vat'];

            $bill->pricing = [
                'type' => $this->getType($bill),
                'fees_percentage' => $percentage,
                'fees_fixed' => $fixed,
                'surebills_fees_percentage' => $this->getType($bill) == 'channel' ? $bill->getPercentage($payment_log, true) : $percentage,
                'surebills_fees_fixed' =>  $this->getType($bill) == 'channel' ? $bill->getFixed($payment_log, true) : $fixed,
                'vat_percentage' => Transaction::VAT_PERCENTAGE,
                'channel_fees_percentage' => $this->getType($bill) == 'channel' ?  $percentage - $bill->getPercentage($payment_log, true) : null,
                'channel_fees_fixed' =>  $this->getType($bill) == 'channel' ? $fixed - $bill->getFixed($payment_log, true) : null,
            ];

            $bill->save();
        }
    }



    protected function paymentSurebillsFees($bill, $log):Array
    {
        if(isset($bill->application) && isset($bill->application->channel)){
            $percentage = $bill->getPercentage($log, true);
            $fixed = $bill->getFixed($log, true);

            $payment_fees = $bill->total * ($percentage / 100) + $fixed;
            $payment_fees_vat = $payment_fees * (Transaction::VAT_PERCENTAGE / 100);
        }else{
            $payment_fees = $bill->payment_fees;
            $payment_fees_vat = $bill->payment_fees_vat;
        }

        return [
            'fees' => $payment_fees,
            'fees_vat' => $payment_fees_vat,
        ];
    }

    protected function getType($bill)
    {
        if($bill->application_id){
            if($bill->application->channel_id){
                return 'channel';
            }
            else{
                return 'application';
            }
        }else{
            return 'user';
        }
    }
    protected function paymentChannelFees($bill, $log):Array
    {
        if(isset($bill->application) && isset($bill->application->channel)){
            $percentage = $bill->getPercentage($log, true);
            $fixed = $bill->getFixed($log, true);

            $p_fees = $bill->total * ($percentage / 100) + $fixed;
            $p_fees_vat = $p_fees * (Transaction::VAT_PERCENTAGE / 100);

            $payment_fees = $bill->payment_fees - $p_fees;
            $payment_fees_vat = $bill->payment_fees_vat - $p_fees_vat;
        }else{
            $payment_fees = null;
            $payment_fees_vat = null;
        }

        return [
            'fees' => $payment_fees,
            'fees_vat' => $payment_fees_vat,
        ];
    }
}
