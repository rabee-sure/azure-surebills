<?php

namespace App\Helpers;
use App\Models\Bill;
use App\Models\PaymentLog;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class PaymentHelper
{
    public static function handlePaymentResponse($invoice, $orderId, $billDetail, $viaWebHook = false)
    {
        if($billDetail['bill']['status'] != 'paid' && $billDetail['bill']['status'] != 'refunded')
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

            $orderResponseJson['id'] = $orderBody->id;
            $orderResponseJson['card']['last4Digits'] = substr($orderBody->sourceOfFunds->provided->card->number, -4);
            $orderResponseJson['result']['code'] = isset($transaction->response->acquirerCode) ? $transaction->response->acquirerCode : null;
            $orderResponseJson['result']['description'] = isset($transaction->response->acquirerMessage) ? $transaction->response->acquirerMessage : null;
            if (isset($orderBody->sourceOfFunds->provided->card->localBrand) && strpos($orderBody->sourceOfFunds->provided->card->localBrand, 'MADA') !== false) {
                $orderResponseJson['paymentBrand'] = 'MADA';
            } else if (isset($orderBody->sourceOfFunds->provided->card->brand)) {
                $orderResponseJson['paymentBrand'] = $orderBody->sourceOfFunds->provided->card->brand;
            }

            PaymentHelper::savePaymentResponse($invoice, $orderResponseJson, $orderBody, $viaWebHook);
        } else if($billDetail['bill']['status'] == 'paid' && $viaWebHook) {
            $bill = Bill::find($orderId);
            $bill->firePaidEvent();
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
            PaymentHelper::checkPaymentStatus($invoice, $payment, $bill, false, true);
        }
    }

    public static function checkPaymentStatus($invoice, $payment, $bill, $apiResponse = false, $viaWebHook = false)
    {
        // if success
        if($invoice->getDetail('success') && $payment->status != 1)
        {
            // log
            $payment->results = $invoice->getDetails();
            $payment->status = 1;
            $payment->save();
            $bill->setPaid();
            if($viaWebHook) {
                $bill->firePaidEvent();
            }

            // get redirect link
            if($bill->application && $bill->is_redirect) {
                $redirect = $bill->getRedirectUrl($payment->results['response']);
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

        //redirect if failed
        if($bill->application && $bill->user->settings->api_bill_style && $bill->is_redirect) {
            $bill->status = 'failed';
            $bill->save();
            return redirect($bill->getRedirectUrl($payment->results['response']));
        } else {
            return redirect()->route('paybillpage', ['id' => $bill->pay_id, 'error' => trans('Payment is Failed')])->withErrors(['field_name' => $invoice->getDetail('description')]);
        }

    }
}
