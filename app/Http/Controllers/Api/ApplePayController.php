<?php

namespace App\Http\Controllers\Api;

use App\Models\Bill;
use App\Models\PaymentLog;
use GuzzleHttp\Client;
use App\Payment\Invoice;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\PaymentHelper;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;

class ApplePayController extends Controller
{
    public function validateMerchant(Request $request)
    {
        $ch = curl_init();
        $data = '{"merchantIdentifier": "'.config('payment.drivers.mastercard_applepay.applepay_merchant_id').'", "domainName":"'.config('payment.drivers.mastercard_applepay.domain').'", "displayName":"SureBills"}';
        curl_setopt($ch, CURLOPT_URL, $request->validationURL);
        curl_setopt($ch, CURLOPT_SSLCERT, base_path('app/Payment/Drivers/MasterCardApplePay/ApplePay.crt.pem'));
        curl_setopt($ch, CURLOPT_SSLKEY, base_path('app/Payment/Drivers/MasterCardApplePay/ApplePay.key.pem'));
        curl_setopt($ch, CURLOPT_SSLKEYPASSWD, config('payment.drivers.mastercard_applepay.passwd'));
        //curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
        //curl_setopt($ch, CURLOPT_SSLVERSION, 'CURL_SSLVERSION_TLSv1_2');
        //curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'rsa_aes_128_gcm_sha_256,ecdhe_rsa_aes_128_gcm_sha_256');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        if(curl_exec($ch) === false)
        {
            echo json_encode('{"curlError":"' . curl_error($ch) . '"}');
        }

        // close cURL resource, and free up system resources
        curl_close($ch);
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
        $client = new Client(['http_errors' => false]);
        $response = $client->put(
            config('payment.drivers.mastercard_applepay.api_base_url').'/'.config('payment.drivers.mastercard_applepay.merchant_id').'/order/'.$bill->id.'/transaction/'.$payment->id,
            [
                'json' => [
                    'apiOperation' => 'PAY',
                    'order' => [
                        'walletProvider' => 'APPLE_PAY',
                        'amount'         => $invoice->getDetails('bill')['bill']['total'],
                        'currency'       => 'SAR',
                        'reference'      => $bill->reference_id
                    ],
                    'sourceOfFunds' => [
                        'type' => 'CARD',
                        'provided' => [
                            'card' => [
                                'devicePayment' => [
                                    'paymentToken' => json_encode($request->paymentToken)
                                ]
                            ]
                        ]
                    ]
                ],
                'auth' => [
                    config('payment.drivers.mastercard_applepay.operator_username'),
                    config('payment.drivers.mastercard_applepay.operator_password')
                ],
            ]
        );
        $response = json_decode($response->getBody()->getContents(), true);

        if (isset($response['result']) && $response['result'] == 'ERROR') {
            $reason = isset($response['error']) && isset($response['error']['explanation']) ? $response['error']['explanation'] : '';
            return [
                'error'    => $reason,
                'redirect' => $bill->pay_url
            ];
        }

        PaymentHelper::handlePaymentResponse($invoice, $bill->id, $invoice->getDetails());

        return PaymentHelper::checkPaymentStatus($invoice, $payment, $bill, true);
    }
}
