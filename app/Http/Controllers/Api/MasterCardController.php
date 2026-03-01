<?php

namespace App\Http\Controllers\Api;

use App\Models\Bill;
use App\Models\PaymentLog;
use GuzzleHttp\Client;
use App\Payment\Invoice;
use Illuminate\Http\Request;
use App\Helpers\PaymentHelper;
use App\Http\Controllers\Controller;

class MasterCardController extends Controller
{
    public function handlePyament(Request $request)
    {
        $bill = Bill::find($request->billId);
        $payment = PaymentLog::create([
            'bill_id'        => $bill->id,
            'payment_method' => 'mastercard_auth',
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
            ->detail(['from_iframe' => $request->from_iframe])
            ->detail(['bill' => $bill->toArray()])
            ->detail(['payment_id' => $payment->id]);


        // Initiate Authentication
        $client = new Client(['http_errors' => false]);
        $response = $client->put(
            config('payment.drivers.mastercard.base_url').'/api/rest/version/57/merchant/'.config('payment.drivers.mastercard.merchant_id').'/order/'.$bill->id.'/transaction/'.$payment->id,
            [
                'json' => [
                    'apiOperation' => 'INITIATE_AUTHENTICATION',
                    'order' => [
                        'currency'       => 'SAR',
                        'reference'      => $bill->reference_id
                    ],
                    'session' => [
                        'id' => $request->paymentToken
                    ],
                    'authentication' => [
                        'acceptVersions' => '3DS1,3DS2',
                        'channel'        => 'PAYER_BROWSER',
                        'purpose'        => 'PAYMENT_TRANSACTION'
                    ]
                ],
                'auth' => [
                    config('payment.drivers.mastercard.operator_username'),
                    config('payment.drivers.mastercard.operator_password')
                ],
            ]
        );
        
        // update payment log
        $response = json_decode($response->getBody()->getContents(), true);
        $payment->results = $response;
        $payment->save();

        // Authenticate Payer
        if (isset($response['result']) && $response['result'] == 'SUCCESS') {
            $client = new Client(['http_errors' => false]);
            $response = $client->put(
                config('payment.drivers.mastercard.base_url').'/api/rest/version/57/merchant/'.config('payment.drivers.mastercard.merchant_id').'/order/'.$bill->id.'/transaction/'.$payment->id,
                [
                    'json' => [
                        'authentication' => [
                            'redirectResponseUrl' => route('mastercard.3ds', [
                                $request->paymentToken,
                            ])
                        ],
                        'apiOperation' => 'AUTHENTICATE_PAYER',
                        'device' => [
                            "browser" => \Request::header('User-Agent'),
                            "browserDetails" => [
                                "3DSecureChallengeWindowSize" => "FULL_SCREEN",
                                "acceptHeaders" => "application/json",
                                "javaEnabled" => true,
                                "language" => \App::getLocale(),
                                "colorDepth" => 24,
                                "screenHeight" => 640,
                                "screenWidth" => 480,
                                "timeZone" => 273
                            ]
                        ],
                        'order' => [
                            'amount'   => number_format($invoice->getDetails('bill')['bill']['total'], 2, '.', ''),
                            'currency' => 'SAR'
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

            // update payment log
            $response = json_decode($response->getBody()->getContents(), true);
            $payment->results = $response;
            $payment->save();

            return $response;
        }

        return [
            'error'    => 'Error: ' . getMastercardError($response),
            'response' => $response,
            'redirect' => $bill->pay_url
        ];
    }

    public function checkPayment(Request $request, $session)
    {
        $authTransaction = PaymentLog::find($request->transaction_id);
        $bill = $authTransaction->bill;
        $payment = PaymentLog::create([
            'bill_id'        => $bill->id,
            'payment_method' => 'mastercard_pay',
            'results'        => [],
            'data'           => [],
            'status'         => 0,
        ]);

        if(!$bill){
            abort(404);
        }

        // prepare invoice
        $invoice = (new Invoice)->amount( number_format($bill->total, 2, '.', ''))
            ->detail(['bill_id' => $bill->id])
            ->detail(['bill' => $bill->toArray()])
            ->detail(['payment_id' => $payment->id]);

        // 3DS Failure
        if ($request->result != 'SUCCESS' || $request->response_gatewayRecommendation != 'PROCEED') {
            return redirect()->route('paybillpage', ['id' => $bill->pay_id, 'error' => '3DS Check Failure'])->withErrors(['field_name' => '3DS Check Failure']);
        }

        // SANDBOX PAYMENT SIMULATION (PAY step, no real MPGS calls)
        if (mastercard_simulation_enabled()) {
            /** @var MasterCardSandboxSimulator $simulator */
            $simulator = app(MasterCardSandboxSimulator::class);
            $fakeResponse = $simulator->simulateSuccessfulPayment($bill, $payment);

            // Save simulated PAYMENT response on the pay log
            $payment->results = $fakeResponse;
            $payment->status = 1;
            $payment->provider_name = 'mastercard';
            $payment->save();

            // Use existing MasterCardService logic to update bill, etc.
            $masterCardService = app(\App\Services\MasterCardService::class);
            $masterCardService->handlePaymentTransaction($fakeResponse, $bill, $payment);

            // Build redirect similar to PaymentHelper::checkPaymentStatus
            if($bill->application && $bill->is_redirect) {
                $redirect = $bill->getRedirectUrl();
            } else {
                $redirect = config('app.url') . '/payment-success';
            }

            return redirect($redirect);
        }

        // make the payment
        $client = new Client(['http_errors' => false]);
        $response = $client->put(
            config('payment.drivers.mastercard.base_url').'/api/rest/version/57/merchant/'.config('payment.drivers.mastercard.merchant_id').'/order/'.$bill->id.'/transaction/'.$payment->id,
            [
                'json' => [
                    'apiOperation' => 'PAY',
                    'authentication' => [
                        'transactionId' => $authTransaction->id
                    ],
                    'order' => [
                        'amount'    => number_format($invoice->getDetails('bill')['bill']['total'], 2, '.', ''),
                        'currency'  => 'SAR',
                        'reference' => $bill->reference_id
                    ],
                    'session' => [
                        'id' => $session
                    ]
                ],
                'auth' => [
                    config('payment.drivers.mastercard.operator_username'),
                    config('payment.drivers.mastercard.operator_password')
                ],
            ]
        );
        $response = json_decode($response->getBody()->getContents(), true);

        if (isset($response['result']) && $response['result'] == 'ERROR') {
            return redirect()->route('paybillpage', ['id' => $bill->pay_id, 'error' => $invoice->getDetail('description')])->withErrors(['field_name' => $invoice->getDetail('description')]);
        }

        PaymentHelper::handlePaymentResponse($invoice, $bill->id, $invoice->getDetails());

        return PaymentHelper::checkPaymentStatus($invoice, $payment, $bill);
    }
}
