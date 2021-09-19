<?php

namespace App\Services;

class BillService 
{
    /**
     * get bills Between Date.
     *
     * @return \Illuminate\Http\Response
     */
    public static function getPaymentChannelFees($bill, $amount = null)
    {
        $total = $amount ?? $bill->total;
        if(isset($bill->application) && isset($bill->application->channel)){
            $percentage =  $bill->pricing['channel_fees_percentage'];
            $fixed = $bill->pricing['channel_fees_fixed'];

            $p_fees = $total * ($percentage / 100) + $fixed;
            $p_fees_vat = $p_fees * ( $bill->pricing['vat_percentage']/ 100);

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

    /**
     * get bills Between Date.
     *
     * @return \Illuminate\Http\Response
     */
    public static function getPaymentSurebillsFees($bill, $amount = null)
    {
        $total = $amount ?? $bill->total;
        if(isset($bill->application) && isset($bill->application->channel)){
            $percentage = $bill->pricing['surebills_fees_percentage'];
            $fixed = $bill->pricing['surebills_fees_fixed'];

            $payment_fees = $total * ($percentage / 100) + $fixed;
            $payment_fees_vat = $payment_fees * ( $bill->pricing['vat_percentage']/ 100);
        }else{
            $payment_fees = $bill->payment_fees;
            $payment_fees_vat = $bill->payment_fees_vat;
        }

        return [
            'fees' => $payment_fees,
            'fees_vat' => $payment_fees_vat,
        ];
    }
}
