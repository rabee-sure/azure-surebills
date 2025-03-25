<?php

namespace App\Http\Controllers;

use App\Http\Requests\CyperSourceProcessPaymentRequest;
use App\Models\Bill;
use App\Models\PaymentLog;
use App\Traits\ExceptionTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\CyberSourceService;
use Illuminate\Support\Facades\Log;
use App\Helpers\BillSignatureHelper;

class PaymentController extends Controller
{
    use ExceptionTrait;
    protected $cyberSourceService;

    public function __construct(CyberSourceService $cyberSourceService)
    {
        $this->cyberSourceService = $cyberSourceService;
    }

    public function healthCheck(Request $request)
    {
        Log::build(['driver' => 'single', 'path' => storage_path('logs/health-check-logs.log'), 'level' => 'debug'])->error(json_encode($request->all()));
        return response()->json($request->all(), 200);
        // Simple response to confirm the webhook endpoint is alive
        return response()->json([
            'status' => 'ok',
            'message' => 'Webhook endpoint is healthy',
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    public function payerAuthSetup(Request $request){
        $bill = Bill::find($request->billId);
        if (!$bill || $bill->is_invalid) {
            abort(404);
        }
       
        try {
            if (!BillSignatureHelper::validateSignature($bill, $request->header('X-Pay-Time'), $request->header('X-Bill-Signature'))) {
                return response()->json(['errors' => ['message' => [trans('Payment Faild')]]], 400);
            }

            $cardData = [
                'card_number' => $request->card_number,
                'card_expiry_month' => $request->card_expiration_month,
                'card_expiry_year' => $request->card_expiration_year,
            ];
            $response = $this->cyberSourceService->payerAuthSetup($cardData);
            if ($response['status'] == 'COMPLETED') {
                return response()->json(['payerAuthSetupRes' => $response, 'status' => 'success'], 200);
            }

            return response()->json(['errors' => ['message' => [trans('Payer Auth Setup Faild')]]], 400);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['errors' => ['message' => [trans('Payer Auth Setup Faild')]]], 400);
        }
    }

    public function checkPayerAuthEnrollment(Request $request){
        $bill = Bill::find($request->billId);
        if (!$bill || $bill->is_invalid) {
            abort(404);
        }
       
        try {
            if (!BillSignatureHelper::validateSignature($bill, $request->header('X-Pay-Time'), $request->header('X-Bill-Signature'))) {
                return response()->json(['errors' => ['message' => [trans('Payment Faild')]]], 400);
            }

            $cardData = [
                'card_number' => $request->card_number,
                'card_expiry_month' => $request->card_expiration_month,
                'card_expiry_year' => $request->card_expiration_year,
            ];
            $response = $this->cyberSourceService->checkPayerAuthEnrollment($bill->fixed_total, $cardData, $request->payerAuthReferenceId);
            if ($response['status'] != "AUTHENTICATION_FAILED") {
                return response()->json(['payerAuthCheckEnrollmentRes' => $response, 'status' => 'success'], 200);
            }

            return response()->json(['errors' => ['message' => [trans('Payer Auth Check Enrollment Faild')]]], 400);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['errors' => ['message' => [trans('Payer Auth Check Enrollment Faild')]]], 400);
        }
    }

    public function validateAuthenticationResults(Request $request){
        $bill = Bill::find($request->billId);
        if (!$bill || $bill->is_invalid) {
            abort(404);
        }
       
        try {
            if (!BillSignatureHelper::validateSignature($bill, $request->header('X-Pay-Time'), $request->header('X-Bill-Signature'))) {
                return response()->json(['errors' => ['message' => [trans('Payment Faild')]]], 400);
            }

            $cardData = [
                'card_number' => $request->card_number,
                'card_expiry_month' => $request->card_expiration_month,
                'card_expiry_year' => $request->card_expiration_year,
            ];
            $authenticationTransactionId = $request->authenticationTransactionId;
            $response = $this->cyberSourceService->validateAuthenticationResults($authenticationTransactionId);
            if ($response['status'] == "AUTHENTICATION_SUCCESSFUL") {
                return response()->json(['payerAuthValidationRes' => $response, 'status' => 'success'], 200);
            }

            return response()->json(['errors' => ['message' => [trans('Payer Auth Validation Faild')]]], 400);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['errors' => ['message' => [trans('Payer Auth Validation Faild')]]], 400);
        }
    }

    /**
     * Process a payment using the CyberSource service.
     *
     * @param CyperSourceProcessPaymentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processPayment(CyperSourceProcessPaymentRequest $request)
    {
        $bill = Bill::find($request->billId);
        if (!$bill || $bill->is_invalid) {
            abort(404);
        }
       
        try {
            if (!BillSignatureHelper::validateSignature($bill, $request->header('X-Pay-Time'), $request->header('X-Bill-Signature'))) {
                return response()->json(['errors' => ['message' => [trans('Payment Faild')]]], 400);
            }

            $response = $this->cyberSourceService->processPayment($bill, ['transit_token' => $request->header('X-Pay-Token'), 'number' => $request->card_number, 'expiration_month' => $request->card_expiration_month, 'expiration_year' => $request->card_expiration_year, 'cvv' => $request->card_cvv]);
            if ($response) {
                if ($bill->application && $bill->application->redirect) {
                    $redirectTo = $bill->redirect_url;
                } else {
                    $redirectTo = url()->to(rtrim(config('app.url'), "/").'/bills/'. $bill->id);
                }

                return response()->json(['redirect_to' => $redirectTo, 'status' => 'success'], 200);
            }

            return response()->json(['errors' => ['message' => [trans('Payment Faild')]]], 400);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['errors' => ['message' => [trans('Payment Faild')]]], 400);
        }
    }

    public function processRefund(Request $request, $transactionId)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'code' => 'required|string',
        ]);

        try {
            $response = $this->cyberSourceService->processRefund($transactionId, $validated['amount'], $validated['code']);

            if ($response) {
                return redirect()->route('bill.show', $transactionId)->with('success', 'Refund successful');
            }

            return response()->json(['message' => 'Refund Faild', 'data' => $response]);
        } catch (\Exception $e) {

            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function handleApplePay(Request $request)
    {
        $applePayToken = $request->input('token');
        $amount = $request->input('amount');
        $currency = $request->input('currency', 'USD');

        try {
            $response = $this->cyberSourceService->processApplePayPayment($applePayToken, $amount, $currency);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        Log::build(['driver' => 'single', 'path' => storage_path('logs/handle-webhook-logs.log'), 'level' => 'debug'])->error(json_encode($request->all()));
        return response()->json(['success' => true], 200);
        //
        $response = $this->cyberSourceService->handleWebhook($request);
        return response()->json($response);
    }
}
