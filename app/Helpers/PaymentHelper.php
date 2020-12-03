<?php

namespace App\Helpers;
use App\Bill;
use App\PaymentLog;
use GuzzleHttp\Client;
use Log;
use IlluminateSupportFacadesLog;

class PaymentHelper
{
    public static function handlePaymentResponse($invoice, $orderId, $billDetail, $viaWebHook = false)
    {

        if($billDetail['bill']['status'] != 'paid')
        {
            $orderBody = PaymentHelper::orderResponse($orderId);
            // dd($orderBody);
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



            // dd($orderResponseJson);





            // $orderResponseJson['id'] = $orderBody->id ?? $orderBody->order->id;
            // $orderResponseJson['card']['bin'] = '';
            // $orderResponseJson['card']['holder'] = $orderBody->sourceOfFunds->provided->card->nameOnCard;
            // $orderResponseJson['card']['binCountry'] = '';
            // $orderResponseJson['card']['expiryYear'] = $orderBody->sourceOfFunds->provided->card->expiry->year;
            // $orderResponseJson['card']['expiryMonth'] = $orderBody->sourceOfFunds->provided->card->expiry->month;
            // $orderResponseJson['card']['last4Digits'] = substr($orderBody->sourceOfFunds->provided->card->number, -4);
            // $orderResponseJson['result']['code'] = is_array($orderBody->transaction) ? $orderBody->transaction[0]->response->acquirerCode : $orderBody->transaction->acquirerCode;
            // $orderResponseJson['result']['description'] = is_array($orderBody->transaction) ? $orderBody->transaction[0]->result : $orderBody->transaction->result;
            // $orderResponseJson['paymentType'] = '';
            // $orderResponseJson['paymentBrand'] = $orderBody->sourceOfFunds->provided->card->brand;
            // $orderResponseJson['merchantTransactionId'] = $billDetail['bill']['id'];
            PaymentHelper::savePaymentResponse($invoice, $orderResponseJson, $orderBody, $viaWebHook);
        }
    }

    public static function orderResponse($orderId)
    {
        $client = new Client();
        $orderResponse = $client->get(config('payment.drivers.mastercard_iframe.api_base_url').'/order/'.$orderId,
                                ['auth' => [config('payment.drivers.mastercard_iframe.operator_username'), config('payment.drivers.mastercard_iframe.operator_password')]]);
        return json_decode($orderResponse->getBody()->getContents(), false);
    }

    public static function savePaymentResponse($invoice, $orderResponseJson, $orderBody, $viaWebHook = false)
    {
        $invoice->detail(['result_code' => $orderResponseJson['result']['code']])
            ->detail(['success' => $orderResponseJson['result']['code'] != null ? 1:0])
            ->detail(['response' => $orderResponseJson])
            ->detail(['description' => $orderResponseJson['result']['description']])
            ->detail(['gateway' => 'mastercard'])
            ->detail(['gateway_response' => $orderBody]);
        $invoice->transactionId(request()->sessionId ?? "not have id");
        $payment = PaymentLog::where('bill_id', $orderResponseJson['merchantTransactionId'])->latest()->first();
        PaymentHelper::checkPaymentStatus($invoice, $payment, $payment->bill);
        Log::debug($payment);

        // if($viaWebHook)
        // {
        //     $payment = PaymentLog::where('bill_id', $orderResponseJson['merchantTransactionId'])->first();
        //     PaymentHelper::checkPaymentStatus($invoice, $payment, $payment->bill);
        // }
    }

    public static function checkPaymentStatus($invoice, $payment, $bill)
    {
        // if success
        if($invoice->getDetail('success')){

            // log
            $payment->results = $invoice->getDetails();
            $payment->status = 1;
            $payment->save();

            $bill->setPaid();

            if($bill->application && $bill->is_redirect){
                return redirect($bill->redirect_url);
            }
            return redirect()->route('paybillpage', ['id' => $bill->pay_id]);
        }

        // log for the payment
        $payment->results = $invoice->getDetails();
        $payment->status = 0;
        $payment->save();

        return redirect()->route('paybillpage', ['id' => $bill->pay_id])->withErrors(['field_name' => $invoice->getDetail('description')]);
    }
}
