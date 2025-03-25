<?php

namespace App\Services;


use App\Abstracts\PaymentAbstract;
use App\Events\BillPaidReversed;
use App\Events\BillPartialRefundedReversed;
use App\Events\BillRefundedReversed;
use App\Http\Resources\CybersourceTransactionDetailsResource;
use App\Jobs\CybersourceGetTransactionDetailJob;
use App\Models\Bill;
use App\Models\PaymentLog;
use CyberSource\Api\CaptureApi;
use CyberSource\Api\PayerAuthenticationApi;
use CyberSource\Api\PaymentsApi;
use CyberSource\Api\RefundApi;
use CyberSource\Api\TransactionDetailsApi;
use CyberSource\ApiClient;
use CyberSource\ApiException;
use CyberSource\Authentication\Core\MerchantConfiguration;
use CyberSource\Configuration;
use CyberSource\Model\CapturePaymentRequest;
use CyberSource\Model\CreatePaymentRequest;
use CyberSource\Model\PayerAuthSetupRequest;
use CyberSource\Model\PtsV2PaymentsCapturesPost201Response;
use CyberSource\Model\Ptsv2paymentsidrefundsClientReferenceInformation;
use CyberSource\Model\Ptsv2paymentsOrderInformation;
use CyberSource\Model\Ptsv2paymentsOrderInformationAmountDetails;
use CyberSource\Model\Ptsv2paymentsOrderInformationBillTo;
use CyberSource\Model\Ptsv2paymentsPaymentInformation;
use CyberSource\Model\Ptsv2paymentsPaymentInformationCard;
use CyberSource\Model\Ptsv2paymentsPaymentInformationFluidData;
use CyberSource\Model\Ptsv2paymentsProcessingInformation;
use CyberSource\Model\PtsV2PaymentsPost201Response;
use CyberSource\Model\PtsV2PaymentsRefundPost201Response;
use CyberSource\Model\RefundPaymentRequest;
use CyberSource\Model\TssV2TransactionsGet200Response;
use CyberSource\Model\Ptsv2paymentsConsumerAuthenticationInformation;
use Exception;
use Illuminate\Support\Facades\Log;
use CyberSource\Api\MicroformIntegrationApi;
use CyberSource\Model\CheckPayerAuthEnrollmentRequest;
use CyberSource\Model\GenerateCaptureContextRequest;
use CyberSource\Model\Ptsv2paymentsPaymentInformationTokenizedCard;
use CyberSource\Model\Ptsv2paymentsTokenInformation;
use CyberSource\Model\Riskv1authenticationresultsConsumerAuthenticationInformation;
use CyberSource\Model\Riskv1authenticationresultsOrderInformation;
use CyberSource\Model\Riskv1authenticationresultsOrderInformationAmountDetails;
use CyberSource\Model\Riskv1authenticationresultsPaymentInformation;
use CyberSource\Model\Riskv1authenticationresultsPaymentInformationCard;
use CyberSource\Model\RiskV1AuthenticationResultsPost201Response;
use CyberSource\Model\Riskv1authenticationsBuyerInformation;
use CyberSource\Model\Riskv1authenticationsetupsClientReferenceInformation;
use CyberSource\Model\Riskv1authenticationsetupsPaymentInformation;
use CyberSource\Model\Riskv1authenticationsetupsPaymentInformationCard;
use CyberSource\Model\Riskv1authenticationsOrderInformation;
use CyberSource\Model\Riskv1authenticationsOrderInformationAmountDetails;
use CyberSource\Model\Riskv1authenticationsOrderInformationBillTo;
use CyberSource\Model\Riskv1authenticationsPaymentInformation;
use CyberSource\Model\Riskv1decisionsClientReferenceInformationPartner;
use CyberSource\Model\Riskv1decisionsConsumerAuthenticationInformation;
use CyberSource\Model\Riskv1authenticationsDeviceInformation;
use CyberSource\Model\RiskV1AuthenticationSetupsPost201Response;
use CyberSource\Model\RiskV1AuthenticationsPost201Response;
use CyberSource\Model\ValidateRequest;

class CyberSourceService extends PaymentAbstract

{
    private $merchantId;
    private $apiKey;
    private $secretKey;
    private $apiUrl;
    private $apiClient;
    protected $providerName = 'cybersource';

    public function __construct()
    {
        $this->apiUrl = config('cybersource.api_url.' . config('cybersource.environment'));

        $config = new Configuration();
        $merchantConfig = new MerchantConfiguration();
        $merchantConfig->setMerchantId(config('cybersource.merchant_id'));
        $merchantConfig->setAuthenticationType('HTTP_SIGNATURE');
        $merchantConfig->setRunEnvironment(config('cybersource.run_environment'));
        $merchantConfig->setSecretKey(config('cybersource.secret_key'));
        $merchantConfig->setApiKeyID(config('cybersource.api_key'));
        $merchantConfig->setIntermediateHost($this->apiUrl);
        $this->apiClient = new ApiClient($config, $merchantConfig);
    }

    /**
     * Creates a microform session for secure payment data capture.
     *
     * @param string $host The target origin (domain) where the microform will be used.
     * @return string|false The generated capture context token on success, or false on failure.
     */
    public function createMicroformSession($host)
    {
        $payload = ['targetOrigins' => [$host], 'clientVersion' => 'v2', 'allowedCardNetworks' => config('cybersource.allowed_card_networks')];
        try {
            $microformIntegrationApi = new MicroformIntegrationApi($this->apiClient);
            return $microformIntegrationApi->generateCaptureContext(new GenerateCaptureContextRequest($payload))[0];
        } catch (ApiException $e) {
            Log::error(json_encode($e->getResponseBody()));
            return false;
        }
    }

    /**
     * Processes a payment using the CyberSource payment gateway.
     *
     * @param Bill $bill The bill object containing the payment details.
     * @param array $cardDetails The credit card details to use for the payment.
     * @return array The response from the CyberSource payment API.
     * @throws Exception If the payment fails.
     */
    public function processPayment($bill, $cardDetails, $payerAuthDetails)
    {
        $payload = $this->preparePaymentPayload($bill, $cardDetails, $payerAuthDetails);
        $initiatePaymentAuthResponse = $this->initiatePaymentAuth($bill, $payload);
        if ($initiatePaymentAuthResponse) {
            return $this->capturePayment($initiatePaymentAuthResponse, $bill, $payload);
        }

        return false;
    }

    public function payerAuthSetup($cardData){
        $api_instance = new PayerAuthenticationApi($this->apiClient);
        $payerAuthSetupRequest = new PayerAuthSetupRequest([
            'paymentInformation' => new Riskv1authenticationsetupsPaymentInformation([
                'card' => new Riskv1authenticationsetupsPaymentInformationCard([
                    // 'type' => '001',
                    'number' => $cardData['card_number'],
                    'expirationMonth' => $cardData['card_expiry_month'],
                    'expirationYear' => $cardData['card_expiry_year'],
                ])
            ])
        ]); // \CyberSource\Model\PayerAuthSetupRequest | 
        
        try {
            $result = $api_instance->payerAuthSetup($payerAuthSetupRequest);
            $resultModel = new RiskV1AuthenticationSetupsPost201Response(json_decode($result[0], true));
            $responseBody = json_decode($resultModel, true);
            return $responseBody;
        } catch (Exception $e) {
            echo 'Exception when calling PayerAuthenticationApi->payerAuthSetup: ', $e->getMessage(), PHP_EOL;
        } finally {
            $this->logResult('cybersource-payer-auth-setup-logs', $responseBody);
        }
    }

    public function checkPayerAuthEnrollment($billAmount, $cardData, $payerSetupRefranceId){
        $api_instance = new PayerAuthenticationApi($this->apiClient);
        $checkPayerAuthEnrollmentRequest = new CheckPayerAuthEnrollmentRequest(
            [
                'orderInformation' => new Riskv1authenticationsOrderInformation([
                    'amountDetails' => new Riskv1authenticationsOrderInformationAmountDetails([
                        'currency' => 'SAR',
                        'totalAmount' => $billAmount,
                    ]),
                ]),
                'paymentInformation' => new Riskv1authenticationsPaymentInformation([
                    'card' => new Riskv1authenticationsetupsPaymentInformationCard([
                        'number' => $cardData['card_number'],
                        // 'type' => '001',
                        'expirationMonth' => $cardData['card_expiry_month'],
                        'expirationYear' => $cardData['card_expiry_year'],
                    ])
                ]),
                'consumerAuthenticationInformation' => new Riskv1decisionsConsumerAuthenticationInformation([
                    'acsWindowSize' => '02',
                    'referenceId' => $payerSetupRefranceId,
                    'transactionMode' => 'S',
                    'returnUrl' => 'https://wv730hw7033250:3002/restapi/cardinalDirect/StepUp/Response'
                ])
            ]
        ); // \CyberSource\Model\CheckPayerAuthEnrollmentRequest |

        try {
            $result = $api_instance->checkPayerAuthEnrollment($checkPayerAuthEnrollmentRequest);
            $resultModel = new RiskV1AuthenticationsPost201Response(json_decode($result[0], true));
            $responseBody = json_decode($resultModel, true);
            return $responseBody;
        } catch (Exception $e) {
            echo 'Exception when calling PayerAuthenticationApi->checkPayerAuthEnrollment: ', $e->getMessage(), PHP_EOL;
        } finally {
            $this->logResult('cybersource-payer-auth-check-enrollment-logs', $responseBody);
        }
    }

    public function validateAuthenticationResults($authenticationTransactionId){
        $api_instance = new PayerAuthenticationApi($this->apiClient);
        $validateRequest = new ValidateRequest([
            'consumerAuthenticationInformation' => new Riskv1authenticationresultsConsumerAuthenticationInformation([
                'authenticationTransactionId' => $authenticationTransactionId,
            ])

        ]); // \CyberSource\Model\ValidateRequest | 

        try {
            $result = $api_instance->validateAuthenticationResults($validateRequest);
            $resultModel = new RiskV1AuthenticationResultsPost201Response(json_decode($result[0], true));
            $responseBody = json_decode($resultModel, true);
            return $responseBody;
        } catch (Exception $e) {
            echo 'Exception when calling PayerAuthenticationApi->validateAuthenticationResults: ', $e->getMessage(), PHP_EOL;
        } finally {
            $this->logResult('cybersource-payer-auth-validate-authentication-logs', $responseBody);
        }
    }

    /**
     * Initiates the payment authorization process with the CyberSource payment gateway.
     *
     * @param Bill $bill The bill object containing the payment details.
     * @param array $cardDetails The credit card details to use for the payment.
     * @param array $payload The payment payload to send to the CyberSource API.
     * @return mixed The response from the CyberSource payment API, or false if the payment fails.
     */
    private function initiatePaymentAuth($bill, $payload)
    {
        $createPaymentResponse = null;
        $paymentLogStatus = false;
        $paymentLogResult = [];
        $paymentLog = $this->createPaymentLog($bill->id, 'mastercard_auth');
        try {
            $paymentsApi = new PaymentsApi($this->apiClient);
            [$createPaymentResponse, $statusCode, $httpHeader] = $paymentsApi->createPayment($payload['paymentRequest']);
            if ($statusCode >= 200 && $statusCode < 300) {
                $paymentLogStatus = true;
            }
            $paymentLogResult = json_decode(new PtsV2PaymentsPost201Response(json_decode($createPaymentResponse, true)), true);
        } catch (ApiException $e) {
            $paymentLogStatus = false;
            $paymentLogResult = (array) $e->getResponseBody();
        } finally {
            $this->logResult('payment-proccess-logs', $paymentLogResult);

            $paymentLogResult['bank_message'] = null;
            if (isset($paymentLogResult['errorInformation']['message'])) {
                $paymentLogResult['bank_message'] = $paymentLogResult['errorInformation']['message'];
            } elseif (isset($paymentLogResult['message'])) {
                $paymentLogResult['bank_message'] = $paymentLogResult['message'];
            }

            $this->updatePaymentLog($paymentLog, $paymentLogResult, $paymentLogStatus);
        }

        return $createPaymentResponse;
    }

    /**
     * Captures the payment for the given payment authorization response, bill, and payment payload.
     *
     * @param mixed $createPaymentResponse The response from the CyberSource payment API after initiating the payment authorization.
     * @param Bill $bill The bill object containing the payment details.
     * @param array $payload The payment payload used to initiate the payment authorization.
     * @return bool True if the payment capture was successful, false otherwise.
     */
    private function capturePayment($createPaymentResponse, $bill, $payload, $paymentMethod = 'mastercard_pay')
    {
        $capturePaymentResponse = null;
        $paymentLogStatus = false;
        $paymentLogResult = [];
        $paymentLog = $this->createPaymentLog($bill->id, 'mastercard_pay');
        try {
            $capture = new CaptureApi($this->apiClient);
            $paymentRequest = new CapturePaymentRequest([
                'clientReferenceInformation' => new Ptsv2paymentsidrefundsClientReferenceInformation(['code' => $createPaymentResponse->getClientReferenceInformation()->getCode()]),
                'orderInformation' => new Ptsv2paymentsOrderInformation([
                    'amountDetails' => $payload['amountDetails'],
                ]),
            ]);
            $capturePaymentResponse = $capture->capturePayment($paymentRequest, $createPaymentResponse->getId());
            $paymentLogResult = json_decode(new PtsV2PaymentsCapturesPost201Response(json_decode($capturePaymentResponse[0], true)), true);
            $paymentLogStatus = true;
        } catch (ApiException $e) {
            $paymentLogStatus = false;
            $paymentLogResult = (array) $e->getResponseBody();
        } finally {
            $this->logResult('cybersource-payment-proccess-logs', $paymentLogResult);

            $paymentLogResult['bank_message'] = null;
            if (isset($paymentLogResult['errorInformation']['message'])) {
                $paymentLogResult['bank_message'] = $paymentLogResult['errorInformation']['message'];
            } elseif (isset($paymentLogResult['message'])) {
                $paymentLogResult['bank_message'] = $paymentLogResult['message'];
            }

            $this->updateBillStatus($bill, $paymentLogStatus, 'payment');
            $this->updatePaymentLog($paymentLog, $paymentLogResult, $paymentLogStatus);
        }

        if ($paymentLogStatus) {
            CybersourceGetTransactionDetailJob::dispatch($paymentLogResult['id'])->delay(now()->addSeconds(10));
        }

        return $paymentLogStatus;
    }

    /**
     * Processes a refund for a payment made through the CyberSource payment gateway.
     *
     * @param Bill $bill The bill object containing the payment details.
     * @param PaymentLog $paymentLog The payment log object containing the details of the payment to be refunded.
     * @param float $amount The amount to be refunded.
     * @return bool True if the refund was successful, false otherwise.
     */
    public function processRefund($bill, $paymentLog, $amount)
    {
        $successRefundFlag = false;
        $paymentResult = [];
        try {
            $billPaymentLog = $bill->payment_logs($paymentLog->bill_id)->where([['webhook_response_received', true], ['is_failure', false], ['provider_name', 'cybersource']])->whereIn('payment_method', ['mastercard_pay', 'mastercard_applepay'])->latest()->first();
            if ($billPaymentLog && $bill->status == 'paid' && $bill->total >= $amount) {
                $refundApi = new RefundApi($this->apiClient);
                $refundRequest = new RefundPaymentRequest([
                    'clientReferenceInformation' => new Ptsv2paymentsidrefundsClientReferenceInformation(['code' => $bill->id]),
                    'orderInformation' => [
                        'amountDetails' => [
                            'totalAmount' => $amount,
                            'currency' => "SAR"
                        ]
                    ]
                ]);

                $refundResponse = $refundApi->refundCapture($refundRequest, $billPaymentLog->results['id']);
                $paymentResult = json_decode(new PtsV2PaymentsRefundPost201Response(json_decode($refundResponse[0], true)), true);
                $successRefundFlag = true;
            } else {
                $successPaymentFlag = false;
            }
        } catch (ApiException $e) {
            $paymentResult = $e->getResponseBody();
            $successRefundFlag = false;
        } finally {
            $this->logResult('refund-proccess-logs', $paymentResult);
            $paymentLog->refunded_amount = $amount;

            $paymentResult['bank_message'] = null;
            if (isset($paymentResult['message'])) {
                $paymentResult['bank_message'] = $paymentResult['message'];
            } else if (isset($paymentResult['status'])) {
                $paymentResult['bank_message'] = $paymentResult['status'];
            }

            $this->updatePaymentLog($paymentLog, $paymentResult, $successRefundFlag);

            if ($successRefundFlag) {
                CybersourceGetTransactionDetailJob::dispatch($paymentResult['id'])->delay(now()->addSeconds(10));
            }
        }

        return $successRefundFlag;
    }

    /**
     * Processes an Apple Pay payment through the CyberSource payment gateway.
     *
     * @param Bill $bill The bill object containing the payment details.
     * @param array $applePayToken The Apple Pay token containing the payment information.
     * @return mixed The result of the payment processing.
     */
    public function processApplePayPayment($bill, $applePayToken)
    {
        $payload = $this->preparePaymentPayload($bill, $applePayToken, 'apple_pay');
        $initiatePaymentAuthResponse = $this->initiatePaymentAuth($bill, $payload);
        if ($initiatePaymentAuthResponse) {
            return $this->capturePayment($initiatePaymentAuthResponse, $bill, $payload, 'mastercard_applepay');
        }

        return false;
    }

    protected function preparePaymentPayload($bill, $cardDetails, $payerAuthDetails, $payloadType = null)
    {
        $processingInformation = $transientTokenJwt = null;
        $paymentInfo = new PtsV2PaymentsPaymentInformation();
        if ($payloadType == 'apple_pay') {
            $this->logResult('fix-apple-pay', json_encode($cardDetails));
            $fluidData = new Ptsv2paymentsPaymentInformationFluidData(['value' => base64_encode(json_encode($cardDetails)), 'descriptor' => 'RklEPUNPTU1PTi5BUFBMRS5JTkFQUC5QQVlNRU5U', 'encoding' => 'Base64']);
            $paymentInfo->setFluidData($fluidData);
            $processingInformation = new Ptsv2paymentsProcessingInformation(['paymentSolution' => '001']);
        } else if (isset($cardDetails['transit_token'])) {
            $transientTokenJwt = new Ptsv2paymentsTokenInformation(['transientTokenJwt' => $cardDetails['transit_token']]);
        } else {
            $paymentInfoCard = new Ptsv2paymentsPaymentInformationCard([
                'number' => $cardDetails['number'],
                'expirationMonth' => $cardDetails['expiration_month'],
                'expirationYear' => $cardDetails['expiration_year'],
                'securityCode' => $cardDetails['cvv'],
            ]);
            $paymentInfo->setCard($paymentInfoCard);
        }

        $amountDetails = new Ptsv2paymentsOrderInformationAmountDetails([
            'totalAmount' => $bill->fixed_total,
            'currency' => "SAR"
        ]);

        $billTo = new Ptsv2paymentsOrderInformationBillTo([
            'locality' => $bill->customer_city ?? 'no city',
            'firstName' => $bill->customer_name ?? 'no name',
            'lastName' => $bill->customer_name ?? 'no name',
            'email' => $bill->customer_email ?? 'bills@surepay.sa',
            'address1' => $bill->customer_buliding_no . " , " . $bill->customer_street_name . " - " . $bill->customer_district,
            'country' => 'SA',
        ]);

        $orderInfo = new Ptsv2paymentsOrderInformation(
            [
                'amountDetails' => $amountDetails,
                'billTo' => $billTo
            ]
        );

        $consumerAuthenticationInformation = new Ptsv2paymentsConsumerAuthenticationInformation([
            'authenticationTransactionId' => $payerAuthDetails['authenticationTransactionId'],
            'cavv' => $payerAuthDetails['cavv'],
            'xid' => $payerAuthDetails['xid'],
            'eciRaw' => $payerAuthDetails['eciRaw'],
        ]);

        $paymentRequest = new CreatePaymentRequest([
            'clientReferenceInformation' => new Ptsv2paymentsidrefundsClientReferenceInformation(['code' => $bill->id]),
            'orderInformation' => $orderInfo,
            'processingInformation' => $processingInformation,
            'tokenInformation' => $transientTokenJwt,
            'paymentInformation' => $paymentInfo,
            'consumerAuthenticationInformation' => $consumerAuthenticationInformation,
        ]);

        return [
            'paymentRequest' => $paymentRequest,
            'amountDetails' => $amountDetails,
        ];
    }

    public function checkTransaction($transactionId)
    {
        try {
            $api_instance = new TransactionDetailsApi($this->apiClient);

            $result = $api_instance->getTransaction($transactionId);
            $resultModel = new TssV2TransactionsGet200Response(json_decode($result[0], true));
            $transactionDetails = json_decode($resultModel, true);
            $this->logResult('transaction-details-logs', $transactionDetails);

            return $transactionDetails;
        } catch (Exception $e) {
            throw new Exception('Exception when calling TransactionDetailsApi->getTransaction: ' . $e->getMessage());
        }
    }

    public function afterSuccefullCapture($transactionDetails)
    {
        $res = new CybersourceTransactionDetailsResource($transactionDetails, true);
        $structureResponse = $res->toArray(request());

        if ($structureResponse['status']) {
            $bill = Bill::where('id', $structureResponse['bill_id'])->first();
            $payment = PaymentLog::where('bank_transaction_id', $transactionDetails['id'])->first();

            $this->completeCycle($structureResponse, $transactionDetails, $bill, $payment);
        } else {
            $this->logResult('transaction-details-logs', $transactionDetails);
        }

        return $structureResponse;
    }

    public function createRevirseTransaction($transactionDetails)
    {
        $res = new CybersourceTransactionDetailsResource($transactionDetails, false);
        $structureResponse = $res->toArray(request());

        if (!$structureResponse['status']) {
            $bill = Bill::where('id', $structureResponse['bill_id'])->first();
            $payment = PaymentLog::where('bank_transaction_id', $transactionDetails['id'])->first();

            if ($structureResponse['type'] == 'payment') {
                event(new BillPaidReversed($bill, $payment));
                $bill->status = 'rejected';
            } elseif ($structureResponse['type'] == 'refund') {
                if ($bill->fixed_total > $structureResponse['amount']) {
                    event(new BillPartialRefundedReversed($bill, $payment, $structureResponse['amount']));
                } elseif ($bill->fixed_total == $structureResponse['amount']) {
                    event(new BillRefundedReversed($bill, $payment));
                }
                $bill->status = 'paid';
                $bill->refund_amount = $bill->refund_amount - $structureResponse['amount'];
                $bill->refunded_at = null;
            }
            $bill->save();

            $this->updatePaymentLog($payment, $structureResponse, $structureResponse['status']);
        } else {
            $this->logResult('reversed-transaction-details-logs', $transactionDetails);
        }

        return $structureResponse;
    }

    /**
     * Logs the result of an operation to a file.
     *
     * @param string $fileName The name of the log file.
     * @param mixed $result The result to be logged.
     */
    private function logResult($fileName, $result)
    {
        Log::build(['driver' => 'single', 'path' => storage_path('logs/' . $fileName . '.log'), 'level' => 'debug'])->error($result);
    }
}
