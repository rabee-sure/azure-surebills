<?php

namespace App\Helpers;
use App\Bill;

class PaymentHelper
{

    // public static function testParam($orderBody)
    // {
    //     // dd($orderBody);
    //     $orderResponseJson['id'] = $orderBody->id;
    //     $orderResponseJson['card']['bin'] = '';
    //     $orderResponseJson['card']['holder'] = $orderBody->sourceOfFunds->provided->card->nameOnCard;
    //     $orderResponseJson['card']['binCountry'] = '';
    //     $orderResponseJson['card']['expiryYear'] = $orderBody->sourceOfFunds->provided->card->expiry->year;
    //     $orderResponseJson['card']['expiryMonth'] = $orderBody->sourceOfFunds->provided->card->expiry->month;
    //     $orderResponseJson['card']['last4Digits'] = substr($orderBody->sourceOfFunds->provided->card->number, -4);
    //     $orderResponseJson['result']['code'] = $orderBody->transaction[0]->response->acquirerCode ?? null;
    //     $orderResponseJson['result']['description'] = $orderBody->transaction[0]->result;
    //     $orderResponseJson['paymentType'] = '';
    //     $orderResponseJson['paymentBrand'] = $orderBody->sourceOfFunds->provided->card->brand;
    //     return $orderResponseJson;
    // }



    public static function handlePaymentResponse($invoice, $orderBody, $billDetail)
    {
        if($billDetail['bill']['status'] != 'paid')
        {
            $orderResponseJson['id'] = $orderBody->id;
            $orderResponseJson['card']['bin'] = '';
            $orderResponseJson['card']['holder'] = $orderBody->sourceOfFunds->provided->card->nameOnCard;
            $orderResponseJson['card']['binCountry'] = '';
            $orderResponseJson['card']['expiryYear'] = $orderBody->sourceOfFunds->provided->card->expiry->year;
            $orderResponseJson['card']['expiryMonth'] = $orderBody->sourceOfFunds->provided->card->expiry->month;
            $orderResponseJson['card']['last4Digits'] = substr($orderBody->sourceOfFunds->provided->card->number, -4);
            $orderResponseJson['result']['code'] = $orderBody->transaction[0]->response->acquirerCode ?? null;
            $orderResponseJson['result']['description'] = $orderBody->transaction[0]->result;
            $orderResponseJson['paymentType'] = '';
            $orderResponseJson['paymentBrand'] = $orderBody->sourceOfFunds->provided->card->brand;
            $orderResponseJson['merchantTransactionId'] = $billDetail['bill']['id'];
            PaymentHelper::savePaymentResponse($invoice, $orderResponseJson, $orderBody);
        }
    }

    public static function savePaymentResponse($invoice, $orderResponseJson, $orderBody)
    {
        $invoice->detail(['result_code' => $orderResponseJson['result']['code']])
            ->detail(['success' => $orderResponseJson['result']['code'] == 00? 1:0])
            ->detail(['response' => $orderResponseJson])
            ->detail(['description' => $orderResponseJson['result']['description']])
            ->detail(['gateway' => 'mastercard'])
            ->detail(['gateway_response' => $orderBody]);
        $invoice->transactionId(request()->sessionId ?? "not have id");
    }
}
