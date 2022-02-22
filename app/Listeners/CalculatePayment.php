<?php

namespace App\Listeners;

use App\Events\BillPaid;
use App\Jobs\MakeTransactionsForChannel;
use App\Jobs\MakeTransactionsForChannelExtraFees;
use App\Jobs\MakeTransactionsForOwner;
use App\Jobs\MakeTransactionsForSureBills;
use App\Mail\SendBillPaidToCustomer;
use App\Models\Bill;
use App\Models\PaymentLog;
use App\Models\Transaction;
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
        $bill = $event->bill;
        $payment_log = $event->payment;

        if($bill && $payment_log){
            $percentage = $bill->getPercentage($payment_log);
            $fixed = $bill->getFixed($payment_log);

            $bill->settled = false;
            $bill->pricing_fees_details = $percentage.'%,'. $fixed;
            $bill->payment_fees = ($bill->total- $bill->channel_extra_amount - $bill->channel_extra_vat) * ($percentage / 100) + $fixed;
            $payment = $this->calculateFeesAndVatIfVatInclusive($bill, $bill->payment_fees);
            $bill->payment_fees_vat = $payment['vat'];
            $bill->payment_fees = $payment['fees'];

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
            
            //make Transactions For Owner.
            MakeTransactionsForOwner::dispatch($bill, $payment_log);

            //make Transactions For Channel
            MakeTransactionsForChannel::dispatch($bill, $payment_log);

            //make Transactions For SureBills
            MakeTransactionsForSureBills::dispatch($bill, $payment_log);
            
            //make Transactions For Channel Extra Fees
            if($bill->channel_extra_amount){
                MakeTransactionsForChannelExtraFees::dispatch($bill, $payment_log);
            }
        }
    }

    protected function paymentSurebillsFees($bill, $log):Array
    {
        if(isset($bill->application) && isset($bill->application->channel)){
            $percentage = $bill->getPercentage($log, true);
            $fixed = $bill->getFixed($log, true);

            $payment_fees = ($bill->total- $bill->channel_extra_amount - $bill->channel_extra_vat) * ($percentage / 100) + $fixed;
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

            $p_fees = ($bill->total- $bill->channel_extra_amount - $bill->channel_extra_vat) * ($percentage / 100) + $fixed;

            $payment = $this->calculateFeesAndVatIfVatInclusive($bill, $p_fees);
            $p_fees = $payment['fees'];
            $p_fees_vat = $payment['vat'];

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

    protected function calculateFeesAndVatIfVatInclusive($bill, $fees)
    {
        if($bill->user->vat_inclusive){
            $vat = $fees - (($fees/(100+Transaction::VAT_PERCENTAGE))*100);
            $fees = (($fees/(100+Transaction::VAT_PERCENTAGE))*100);
        }else{
            $vat = $fees * (Transaction::VAT_PERCENTAGE / 100);
        }
        return [
            'vat' => $vat,
            'fees' => $fees
        ];
    }
}
