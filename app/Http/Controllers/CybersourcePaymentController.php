<?php

namespace App\Http\Controllers;

use App\Http\Requests\CyperSourceProcessPaymentRequest;
use App\Models\Bill;
use Illuminate\Http\Request;
use App\Services\CyberSourceService;

class CybersourcePaymentController extends Controller
{
    private $cyberSourceService;

    public function __construct(CyberSourceService $cyberSourceService)
    {
        $this->cyberSourceService = $cyberSourceService;
    }

    public function processPayment(CyperSourceProcessPaymentRequest $request)
    {
        $bill = Bill::find($request->bill_id);
        if (!$bill || $bill->is_invalid) {
            abort(404);
        }
        try {
            $response = $this->cyberSourceService->processPayment($bill, ['number' => $request->card_number, 'expiration_month' => $request->exp_month, 'expiration_year' => $request->exp_year, 'cvv' => $request->cvv]);
            if ($response) {
                return response()->json(['redirect_to' => route('bill.show', $bill->id), 'status' => 'success'], 200);
            }
            return response()->json(['errors' => ['message' => [trans('Payment Faild')]], 'data' => $response], 400);
        } catch (\Exception $e) {
            return true;
            return response()->json(['errors' => ['message' => [$e->getMessage()]]], 400);
        }
    }
}
