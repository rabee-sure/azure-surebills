<?php

namespace App\Http\Controllers\Api;

use App\Helpers\BillSignatureHelper;
use App\Models\Bill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CybersourcePayViaApplePayRequest;
use App\Services\CyberSourceService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class CybersourceApplePayController extends Controller
{
    private $cybersourceService;
    public function __construct(CyberSourceService $cybersourceService)
    {
        $this->cybersourceService = $cybersourceService;
    }

    /**
     * Validates the merchant for Apple Pay transactions.
     *
     * @param Request $request
     * @return void
     */
    public function validateMerchant(Request $request)
    {
        $ch = curl_init();
        $data = '{"merchantIdentifier": "' . config('payment.drivers.cybersource_applepay.applepay_merchant_id') . '", "domainName":"'.$request->host.'", "displayName":"SureBills"}';
        curl_setopt($ch, CURLOPT_URL, $request->validationURL);
        curl_setopt($ch, CURLOPT_SSLCERT, base_path('app/Payment/Drivers/CybersourceApplePay/applepay.crt.pem'));
        curl_setopt($ch, CURLOPT_SSLKEY, base_path('app/Payment/Drivers/CybersourceApplePay/applepay.key.pem'));    
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        if (curl_exec($ch) === false) {
            echo json_encode('{"curlError":"' . curl_error($ch) . '"}');
            Log::error('apple pay error: '.curl_error($ch));
            Log::error('apple pay error: '.$request->validationURL);
            Log::error('apple pay error: '.$data);
        }


        // close cURL resource, and free up system resources
        curl_close($ch);
    }

    /**
     * Checks the payment made via Apple Pay.
     *
     * @param CybersourcePayViaApplePayRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPayment(CybersourcePayViaApplePayRequest $request)
    {
        $this->cybersourceService->logResult('process-payment-cards', "here check payment in apple pay");
        $bill = Bill::find($request->billId);
        if (!$bill || $bill->is_invalid) {
            abort(404);
        }
        
        if($bill && $bill->status == 'pending')
        {
            $response = $this->payerAuthSetup($request, $bill);
            return $response;
            
            // $response = $this->cybersourceService->processApplePayPayment($bill, $request->paymentToken);
            // if($response == false)
            // {
            //     return response()->json(['status' => 'fail'], 400);    
            // }
            
            // return response()->json(['status' => 'success'], 200);    
        }

        return response()->json(['status' => 'fail'], 400);    
    }

    private function payerAuthSetup($request, $bill)
    {
        try {
            if (!BillSignatureHelper::validateSignature($bill, $request->header('X-Pay-Time'), $request->header('X-Bill-Signature'))) {
                return response()->json(['errors' => ['message' => [trans('Payment Faild')]]], 400);
            }

            $cardData = $request->paymentToken;

            Cache::put('card_data_' . $bill->id, Crypt::encrypt($cardData), now()->addMinutes(10));

            $response = $this->cybersourceService->payerAuthSetup($cardData, true);
            if ($response['status'] == 'COMPLETED') {
                return response()->json(['payerAuthSetupRes' => $response, 'status' => 'success'], 200);
            }

            return response()->json(['errors' => ['message' => [trans('Payer Auth Setup Faild')]]], 400);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['errors' => ['message' => [trans('Payer Auth Setup Faild')]]], 400);
        }
    }
}
