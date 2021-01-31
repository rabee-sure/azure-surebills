<?php

namespace App\Http\Controllers\Api;

use App\Bill;
use App\PaymentLog;
use GuzzleHttp\Client;
use App\Payment\Invoice;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\PaymentHelper;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;

class MasterCardController extends Controller
{
    public function handlePyament(Request $request)
    {
        $bill = Bill::find($request->billId);
        $payment = PaymentLog::create([
            'bill_id'        => $bill->id,
            'payment_method' => 'mastercard_applepay',
            'results'        => [],
            'data'           => [],
            'status'         => 0,
        ]);

        if(!$payment || !$bill || $bill->is_invalid){
            abort(404);
        }

        // prepare invoice
        $invoice = (new Invoice)->amount( number_format($bill->total, 2, '.', ''))
            ->detail(['bill_id' => $bill->id])
            ->detail(['bill' => $bill->toArray()])
            ->detail(['payment_id' => $payment->id]);

        // check payment
        // try {
            $client = new Client();
            $response = $client->put(
                config('payment.drivers.mastercard.base_url').'/api/rest/version/57/merchant/'.config('payment.drivers.mastercard.merchant_id').'/order/'.$payment->id.'/transaction/'.$payment->id,
                [
                    'json' => [
                        'apiOperation' => 'INITIATE_AUTHENTICATION',
                        'order' => [
                            'currency'       => 'SAR',
                            'reference'      => $bill->id
                        ],
                        'session' => [
                            'id' => $request->paymentToken
                        ]
                    ],
                    'auth' => [
                        config('payment.drivers.mastercard.operator_username'),
                        config('payment.drivers.mastercard.operator_password')
                    ],
                ]
            );
            $response = json_decode($response->getBody()->getContents(), true);
            dd($response);
        // } catch (ClientException $e) {
        //     $response = json_decode($e->getResponse()->getBody()->getContents(), true);
        // } catch (\Exception $e) {
        //     $response = $e->getMessage();
        // }

        if (isset($response['result']) && $response['result'] == 'ERROR') {
            $reason = isset($response['error']) && isset($response['error']['explanation']) ? $response['error']['explanation'] : '';
            return [
                'error'    => $reason,
                'redirect' => $bill->pay_url
            ];
        }

        PaymentHelper::handlePaymentResponse($invoice, $payment->id, $invoice->getDetails());

        return PaymentHelper::checkPaymentStatus($invoice, $payment, $bill, true);
    }

    public function checkPayment(Request $request)
    {
        $bill = Bill::find($request->billId);
        $payment = PaymentLog::create([
            'bill_id'        => $bill->id,
            'payment_method' => 'mastercard_applepay',
            'results'        => [],
            'data'           => [],
            'status'         => 0,
        ]);

        if(!$payment || !$bill || $bill->is_invalid){
            abort(404);
        }

        // prepare invoice
        $invoice = (new Invoice)->amount( number_format($bill->total, 2, '.', ''))
            ->detail(['bill_id' => $bill->id])
            ->detail(['bill' => $bill->toArray()])
            ->detail(['payment_id' => $payment->id]);

        // check payment
        // try {
            $client = new Client();
            $response = $client->put(
                config('payment.drivers.mastercard.base_url').'/api/rest/version/57/merchant/'.config('payment.drivers.mastercard.merchant_id').'/order/'.$payment->id.'/transaction/'.$payment->id,
                [
                    'json' => [
                        'apiOperation' => 'PAY',
                        'order' => [
                            'amount'         => $invoice->getDetails('bill')['bill']['total'],
                            'currency'       => 'SAR',
                            'reference'      => $bill->id
                        ],
                        'session' => [
                            'id' => $request->paymentToken
                        ]
                        // 'sourceOfFunds' => [
                        //     'type' => 'CARD',
                        //     'provided' => [
                        //         'card' => [
                        //             'devicePayment' => [
                        //                 'paymentToken' => json_encode($request->paymentToken)
                        //             ]
                        //         ]
                        //     ]
                        // ]
                    ],
                    'auth' => [
                        config('payment.drivers.mastercard.operator_username'),
                        config('payment.drivers.mastercard.operator_password')
                    ],
                ]
            );
            // $response = json_decode($response->getBody()->getContents(), true);
            dd($response->getBody()->getContents());
        // } catch (ClientException $e) {
        //     $response = json_decode($e->getResponse()->getBody()->getContents(), true);
        // } catch (\Exception $e) {
        //     $response = $e->getMessage();
        // }

        if (isset($response['result']) && $response['result'] == 'ERROR') {
            $reason = isset($response['error']) && isset($response['error']['explanation']) ? $response['error']['explanation'] : '';
            return [
                'error'    => $reason,
                'redirect' => $bill->pay_url
            ];
        }

        PaymentHelper::handlePaymentResponse($invoice, $payment->id, $invoice->getDetails());

        return PaymentHelper::checkPaymentStatus($invoice, $payment, $bill, true);
    }
}
