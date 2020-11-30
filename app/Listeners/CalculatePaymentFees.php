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
        if($event->bill->success_payment){
            $bill = $event->bill;
            $percentage = $this->getPercentage($bill);
            $fixed = $this->getFixed($bill);

            $bill->settled = false;
            $bill->pricing_fees_details = $percentage.'%,'. $fixed;
            $bill->payment_fees = $bill->total * ($percentage / 100) + $fixed;
            $bill->payment_fees_vat = $bill->payment_fees * (Transaction::VAT_PERCENTAGE / 100);
            $bill->save();

            if(isset($bill->application) && isset($bill->application->channel)){
                $this->makeTransactionsForChannel($bill);
            }
        }
    }


    /**
     * make Transactions For Channel.
     *
     * @param  BillPaid  $event
     * @return void
     */
    protected function makeTransactionsForChannel($bill)
    {
        $percentage = $this->getPercentage($bill, true);
        $fixed = $this->getFixed($bill, true);

        $payment_fees = $bill->total * ($percentage / 100) + $fixed;
        $payment_fees_vat = $payment_fees * (Transaction::VAT_PERCENTAGE / 100);

        // $data = [
        //     'channel' => $bill->application->channel,
        //     'percentage' => $percentage,
        //     'fixed' => $fixed,
        //     'payment_fees' => $payment_fees,
        //     'payment_fees_vat' => $payment_fees_vat,
        //     'bill_payment_fees' => $bill->payment_fees,
        //     'bill_payment_fees_vat' => $bill->payment_fees_vat,
        // ];

        // dd($data);

        $fee_trans = new Transaction;
        $fee_trans->user_id     = $bill->application->channel->user_id;
        $fee_trans->bill_id     = $bill->id;
        $fee_trans->type        = 'credit';
        $fee_trans->amount      = $bill->payment_fees - $payment_fees;
        $fee_trans->reference   = $bill->number;
        $fee_trans->receipt     = $fee_trans->generateReceipt();
        $fee_trans->description = 'Fee - Channel: '.$bill->application->channel->name;
        $fee_trans->balance     = $fee_trans->user->balance + $fee_trans->amount;
        $fee_trans->save();

        $vat_trans = new Transaction;
        $vat_trans->user_id     = $bill->application->channel->user_id;
        $vat_trans->bill_id     = $bill->id;
        $vat_trans->type        = 'credit';
        $vat_trans->amount      = $bill->payment_fees_vat - $payment_fees_vat;
        $vat_trans->reference   = $bill->number;
        $vat_trans->receipt     = $vat_trans->generateReceipt();
        $vat_trans->description = 'Vat - Channel: '.$bill->application->channel->name;
        $vat_trans->balance     = $vat_trans->user->balance + $vat_trans->amount;
        $vat_trans->save();
    }

    /**
     * get Percentage from object.
     *
     * @return double
     */
    protected function getPercentage($bill, $from_channel = false)
    {
        $response = $bill->success_payment->results['response'];
        if( isset($bill->application) && isset($bill->application->channel)) {
            $object = $from_channel ? $bill->application->channel : $bill->application;
        }else{
            $object = $bill->user;
        }
        if(isset($response['paymentBrand']) && $response['paymentBrand'] == 'MADA'){
            return $object->mada_percentage;
        }else{
            return $object->credit_cards_percentage;
        }
    }

    /**
     * get Fixed from object.
     *
     * @return double
     */
    protected function getFixed($bill, $from_channel = false)
    {
        $response = $bill->success_payment->results['response'];
        if( isset($bill->application) && isset($bill->application->channel)) {
            $object = $from_channel ? $bill->application->channel : $bill->application;
        }else{
            $object = $bill->user;
        }
        if(isset($response['paymentBrand']) && $response['paymentBrand'] == 'MADA'){
            return $object->mada_fixed;
        }else{ 
            return $object->credit_cards_fixed;
        }
    }
}
