<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CyberSource\ApiClient;
use CyberSource\Api\PayerAuthenticationApi;
use CyberSource\Api\PaymentsApi;
use CyberSource\Model\PayerAuthSetupRequest;
use CyberSource\Model\PayerAuthValidateRequest;
use CyberSource\Model\CreatePaymentRequest;
use CyberSource\Configuration;
use CyberSource\Authentication\Core\MerchantConfiguration;
use CyberSource\Model\Riskv1authenticationsetupsPaymentInformation;
use CyberSource\Model\Riskv1authenticationsetupsPaymentInformationCard;
use CyberSource\Model\RiskV1AuthenticationSetupsPost201Response;
use Illuminate\Support\Str;

class CyberSourceController extends Controller
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

    public function showPaymentForm(Request $request)
    {
        // Get order details from request or session
        $amount = $request->input('amount', '100.00');
        $currency = $request->input('currency', 'USD');
        $orderId = $request->input('order_id', Str::uuid()->toString());
        return view('payment.form', compact('amount', 'currency', 'orderId'));
    }

    /**
     * Initiate payment with 3DS
     */
    public function initiatePayment(Request $request)
    {
        // Validate request data
        $validated = $request->validate([
            'card_number' => 'required|string',
            'expiration_month' => 'required|string|size:2',
            'expiration_year' => 'required|string|size:4',
            'cvv' => 'required|string',
            'amount' => 'required|numeric',
            'currency' => 'required|string|size:3',
            'order_id' => 'nullable|string',
        ]);
        
        // Generate a unique reference ID for this transaction
        $referenceId = $validated['order_id'] ?? Str::uuid()->toString();
        
        // Store payment info in session for later use after 3DS authentication
        session([
            'payment_info' => [
                'reference_id' => $referenceId,
                'card_number' => $validated['card_number'],
                'expiration_month' => $validated['expiration_month'],
                'expiration_year' => $validated['expiration_year'],
                'cvv' => $validated['cvv'],
                'amount' => $validated['amount'],
                'currency' => $validated['currency'],
            ]
        ]);
        
        // Return URL after 3DS authentication
        $returnUrl = route('payment.post-authentication');
        
        // Step 1: Setup 3DS authentication
        $setupResponse = $this->threeDSecureService->setupAuthentication(
            $referenceId,
            $validated['card_number'],
            $returnUrl
        );
        
        if ($setupResponse['status'] === 'error') {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to setup 3DS authentication',
                    'details' => $setupResponse
                ], 400);
            }
            
            return redirect()->back()->with('error', 'Failed to setup 3DS authentication');
        }
        
        // Store access token in session
        session(['access_token' => $setupResponse['access_token']]);
        
        // Return the response based on request type
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'reference_id' => $referenceId,
                'access_token' => $setupResponse['access_token'],
                'step_up_url' => $setupResponse['response']->getConsumerAuthenticationInformation()->getStepUpUrl() ?? null,
                'message' => '3DS authentication initiated. Proceed with challenge.',
            ]);
        }
        
        // For non-AJAX requests, redirect to the step-up URL
        $stepUpUrl = $setupResponse['response']->getConsumerAuthenticationInformation()->getStepUpUrl();
        if ($stepUpUrl) {
            return redirect()->to($stepUpUrl);
        }
        
        // If no step-up URL, process payment directly
        return $this->handlePostAuthentication($request);
    }
        
    /**
     * Handle return from 3DS authentication
     */
    public function handlePostAuthentication(Request $request)
    {
        // Get the transaction ID from the request
        $transactionId = $request->input('transaction_id');
        $referenceId = $request->input('reference_id') ?? session('payment_info.reference_id');
        
        if (!$transactionId || !$referenceId) {
            Log::error('Missing transaction ID or reference ID', [
                'transaction_id' => $transactionId,
                'reference_id' => $referenceId,
                'session_data' => session()->all()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Missing transaction ID or reference ID'
                ], 400);
            }
            
            return redirect()->route('payment.complete')->with([
                'payment_status' => 'error',
                'error_message' => 'Missing transaction ID or reference ID'
            ]);
        }
        
        // Validate the authentication
        $validationResponse = $this->threeDSecureService->validateAuthentication(
            $referenceId,
            $transactionId
        );
        
        if ($validationResponse['status'] === 'error') {
            Log::error('Failed to validate 3DS authentication', $validationResponse);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to validate 3DS authentication',
                    'details' => $validationResponse
                ], 400);
            }
            
            return redirect()->route('payment.complete')->with([
                'payment_status' => 'error',
                'error_message' => 'Failed to validate 3DS authentication'
            ]);
        }
        
        // Check if authentication was successful
        $authResult = $validationResponse['authentication_result'];
        
        if ($authResult !== '0' && $authResult !== '1') {
            Log::warning('Authentication failed or declined', [
                'auth_result' => $authResult,
                'validation_response' => $validationResponse
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Authentication failed',
                    'auth_result' => $authResult
                ], 400);
            }
            
            return redirect()->route('payment.complete')->with([
                'payment_status' => 'error',
                'error_message' => 'Authentication failed or was declined'
            ]);
        }
        
        // Get payment info from session
        $paymentInfo = session('payment_info');
        
        if (!$paymentInfo) {
            Log::error('Payment info not found in session');
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment information not found'
                ], 400);
            }
            
            return redirect()->route('payment.complete')->with([
                'payment_status' => 'error',
                'error_message' => 'Payment information not found'
            ]);
        }
        
        // Add authentication data to payment info
        $paymentInfo['auth_cavv'] = $validationResponse['cavv'];
        $paymentInfo['auth_xid'] = $validationResponse['xid'];
        $paymentInfo['auth_result'] = $validationResponse['authentication_result'];
        
        // Now process the payment with 3DS data
        $paymentResponse = $this->paymentService->processPayment($paymentInfo);
        
        // Clear sensitive data from session
        session()->forget(['payment_info', 'access_token']);
        
        // Prepare response based on payment result
        if ($paymentResponse['status'] === 'success') {
            $sessionData = [
                'payment_status' => 'success',
                'transaction_id' => $paymentResponse['transaction_id'],
                'auth_code' => $paymentResponse['auth_code'],
                'amount' => $paymentInfo['amount'],
                'currency' => $paymentInfo['currency']
            ];
        } else if ($paymentResponse['status'] === 'pending') {
            $sessionData = [
                'payment_status' => 'pending',
                'transaction_id' => $paymentResponse['transaction_id'] ?? null,
            ];
        } else {
            $sessionData = [
                'payment_status' => 'error',
                'error_message' => $paymentResponse['message'] ?? 'Payment processing failed'
            ];
        }
        
        if ($request->expectsJson()) {
            return response()->json(array_merge(
                ['status' => $paymentResponse['status']],
                $sessionData
            ));
        }
        
        // For non-AJAX requests, redirect to completion page
        return redirect()->route('payment.complete')->with($sessionData);
    }

    /**
     * Show payment completion page
     */
    public function showCompletionPage()
    {
        return view('payment.complete');
    }

}
