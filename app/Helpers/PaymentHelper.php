<?php

namespace App\Helpers;
use App\Models\Bill;
use App\Models\PaymentLog;
use GuzzleHttp\Client;
use App\Jobs\MastercardWebhookSimulation;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

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

            MastercardWebhookSimulation::dispatch($bill->id, $payment->id)->delay(now()->addMinutes(config('mastercard.webhook_simulation_delay_in_minutes')));

            if($viaWebHook) {
                $bill->firePaidEvent();
            }

            // get redirect link
            if($bill->application && $bill->is_redirect) {
                $redirect = $bill->getRedirectUrl($payment->results['response']);
            } else {
                $redirect = config('app.url') . '/payment-success';
            }

            if ($apiResponse) {
                return [
                    'redirect' => $redirect
                ];
            }

            return self::renderPostPaymentRedirect($redirect);
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
            return self::renderPostPaymentRedirect($bill->getRedirectUrl($payment->results['response']));
        } else {
            return self::renderPostPaymentRedirect(
                route('paybillpage', ['id' => $bill->pay_id, 'error' => trans('Payment is Failed')]),
                ['field_name' => $invoice->getDetail('description')]
            );
        }

    }

    /**
     * Render a 200-OK HTML interstitial that performs a client-side redirect
     * to `$url` instead of issuing an HTTP 302.
     *
     * Why: CSP `form-action` (Level 3) is enforced on EVERY URL in the
     * redirect chain that follows a form submission. The Mastercard 3DS
     * auto-submitted form posts to our `check-payment` endpoint and we then
     * redirect to either the merchant's `back_url` (arbitrary URL we cannot
     * whitelist) or our own pay/success pages. Returning this interstitial
     * terminates the form-submission navigation on our host (which IS in
     * `form-action`); the subsequent `window.location.replace(...)` is a
     * regular navigation that `form-action` does not gate.
     *
     * @param  string  $url
     * @param  array<string,string>  $errors  Optional validation errors to
     *         flash to the session (mirrors `redirect()->withErrors([...])`),
     *         so the destination Blade can still display them via `$errors`.
     * @return \Illuminate\Contracts\View\View
     */
    public static function renderPostPaymentRedirect($url, array $errors = [])
    {
        if (!empty($errors)) {
            $bag = session()->get('errors', new ViewErrorBag())
                ->put('default', new MessageBag($errors));
            session()->flash('errors', $bag);
        }

        return view('bills.payment_redirect', ['redirectUrl' => $url]);
    }
}
