<?php

namespace App\Helpers;
use App\Bill;
use App\PaymentLog;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class PaymentHelper
{
    public static function handlePaymentResponse($invoice, $orderId, $billDetail, $viaWebHook = false)
    {
        if($billDetail['bill']['status'] != 'paid')
        {
            // mastercard response
            $client = new Client();
            $orderResponse = $client->get(
                config('payment.drivers.mastercard_iframe.api_base_url').'/order/'.$orderId,
                [
                    'auth' => [
                        config('payment.drivers.mastercard_iframe.operator_username'),
                        config('payment.drivers.mastercard_iframe.operator_password')
                    ]
                ]
            );
            $orderBody = json_decode($orderResponse->getBody()->getContents(), false);
            $transaction = $orderBody->transaction[count($orderBody->transaction)-1];
            Log::emergency(json_encode($orderBody));

            $orderResponseJson['id'] = $orderBody->id;
            $orderResponseJson['card']['last4Digits'] = substr($orderBody->sourceOfFunds->provided->card->number, -4);
            $orderResponseJson['result']['code'] = isset($transaction->response->acquirerCode) ? $transaction->response->acquirerCode : null;
            $orderResponseJson['result']['description'] = isset($transaction->response->acquirerMessage) ? $transaction->response->acquirerMessage : null;
            if (isset($orderBody->sourceOfFunds->provided->card->localBrand) && strpos($orderBody->sourceOfFunds->provided->card->localBrand, 'MADA') !== false) {
                $orderResponseJson['paymentBrand'] = 'MADA';
            } else {
                $orderResponseJson['paymentBrand'] = $orderBody->sourceOfFunds->provided->card->brand;
            }

            PaymentHelper::savePaymentResponse($invoice, $orderResponseJson, $orderBody, $viaWebHook);
        }
    }

    public static function savePaymentResponse($invoice, $orderResponseJson, $orderBody, $viaWebHook = false)
    {
        $transaction = $orderBody->transaction[count($orderBody->transaction)-1];
        $bill        = Bill::find($orderBody->id);
        $payment     = PaymentLog::find($transaction->transaction->id);

        $invoice->detail(['result_code' => $orderResponseJson['result']['code']])
            ->detail(['success' => ($transaction->order->status == 'CAPTURED' && $transaction->order->amount == $bill->total) ? true : false])
            ->detail(['response' => $orderResponseJson])
            ->detail(['description' => $orderResponseJson['result']['description']])
            ->detail(['gateway' => $payment->payment_method])
            ->detail(['gateway_response' => $orderBody]);
        $invoice->transactionId($payment->id);

        if($viaWebHook) {
            PaymentHelper::checkPaymentStatus($invoice, $payment, $bill);
        }
    }

    public static function checkPaymentStatus($invoice, $payment, $bill, $apiResponse = false)
    {
        // if success
        if($invoice->getDetail('success'))
        {
            // log
            $payment->results = $invoice->getDetails();
            $payment->status = 1;
            $payment->save();
            $bill->setPaid();

            // get redirect link
            if($bill->application && $bill->is_redirect) {
                $redirect = $bill->redirect_url;
            } else {
                $redirect = route('paybillpage', ['id' => $bill->pay_id]);
            }

            if ($apiResponse) {
                return [
                    'redirect' => $redirect
                ];
            }

            return redirect($redirect);
        }

        // log for the payment
        $payment->results = $invoice->getDetails();
        $payment->status = 0;
        $payment->save();


        if ($apiResponse) {
            return [
                'error'    => $invoice->getDetail('description'),
                'redirect' => route('paybillpage', ['id' => $bill->pay_id]),
            ];
        }

        return redirect()->route('paybillpage', ['id' => $bill->pay_id])->withErrors(['field_name' => $invoice->getDetail('description')]);
    }
}
