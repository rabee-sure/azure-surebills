<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Bill;
use App\Models\Transfer;
use App\Models\PaymentLog;
use App\Models\Transaction;
use App\Models\TransferLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\TransferOperations;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\UpdateTransferExcelFile;
use Illuminate\Support\Facades\Storage;
use App\Jobs\CreateTransferExcelFileJob;
use App\Exports\TransactionsExportQueued;
use App\Http\Resources\TransactionExportResource;

class MasterCardService
{
    public function handleWebhook($request)
    {
        if (!$this->checkMastercardSignature($request)) {
            return false;
        }

        // handle response
        $response = $request->all();
        if (isset($response['order'])
            && isset($response['order']['id'])
            && isset($response['transaction'])
            && isset($response['transaction']['id'])
            && isset($response['transaction']['type'])
        ) {

            // get bill & payment
            $bill = Bill::find($response['order']['id']);
            $payment = PaymentLog::find($response['transaction']['id']);
            if ($bill && $payment) {

                // handle PAYMENT transaction
                if ($response['transaction']['type'] == "PAYMENT") {
                    try {
                        return $this->handlePaymentTransaction($response, $bill, $payment);
                    } catch (\Exception $e) {
                        Log::emergency("payment issue");
                        Log::emergency($e->getMessage());
                        Log::emergency(json_encode($e));
                    }
                }

                // handle REFUND transaction
                if ($response['transaction']['type'] == "REFUND") {
                    try {
                        return $this->handleRefundTransaction($response, $bill, $payment);
                    } catch (\Exception $e) {
                        Log::emergency("refund issue");
                        Log::emergency($e->getMessage());
                        Log::emergency(json_encode($e));
                    }
                }
            }
        }

        return false;
    }

    /*
    * handle PAYMENT transaction
    *
    */
    private function handlePaymentTransaction($response, $bill, $payment)
    {
        // data
        $bank_message = $response['response']['acquirerMessage'] ?? null;
        $bank_transaction_id = $response['transaction']['acquirer']['transactionId'] ?? null;
        if (isset($response['sourceOfFunds']['provided']['card']['localBrand'])) {
            $brand = 'MADA';
            $card_number = $response['sourceOfFunds']['provided']['card']['deviceSpecificNumber'] ?? null;
        } else {
            $brand = $response['sourceOfFunds']['provided']['card']['brand'];
            $card_number = $response['sourceOfFunds']['provided']['card']['number'];
        }

        if ($response['result'] == "SUCCESS" && $response['response']['gatewayCode'] == "APPROVED") {
            $payment->results = $response;
            $payment->status = true;
            $payment->brand = $brand;
            $payment->card_number = $card_number;
            $payment->bank_transaction_id = $bank_transaction_id;
            $payment->bank_message = $bank_message;
            $payment->webhook_response_received = true;
            $payment->save();
            $bill->setPaid();

            $bill->firePaidEvent($payment);
        } else {
            $payment->results = $response;
            $payment->status = false;
            $payment->brand = $brand;
            $payment->card_number = $card_number;
            $payment->bank_transaction_id = $bank_transaction_id;
            $payment->bank_message = $bank_message;
            $payment->webhook_response_received = true;
            $payment->save();

            // set failed if coming from the app
            if($bill->application && $bill->is_redirect) {
                $bill->status = 'failed';
                $bill->save();
            }
        }

        return true;
    }

    /*
    * handle REFUND transaction
    *
    */
    private function handleRefundTransaction($response, $bill, $payment)
    {
        // data
        $bank_message = $response['response']['acquirerMessage'] ?? null;
        $bank_transaction_id = $response['transaction']['acquirer']['transactionId'] ?? null;
        if (isset($response['sourceOfFunds']['provided']['card']['localBrand'])) {
            $brand = 'MADA';
            $card_number = $response['sourceOfFunds']['provided']['card']['deviceSpecificNumber'] ?? null;
        } else {
            $brand = $response['sourceOfFunds']['provided']['card']['brand'];
            $card_number = $response['sourceOfFunds']['provided']['card']['number'];
        }

        if ($response['result'] == "SUCCESS" && $response['response']['gatewayCode'] == "APPROVED") {
            $payment->results = $response;
            $payment->status = true;
            $payment->brand = $brand;
            $payment->card_number = $card_number;
            $payment->bank_transaction_id = $bank_transaction_id;
            $payment->bank_message = $bank_message;
            $payment->webhook_response_received = true;
            $payment->save();

            if ($bill->total == $response['transaction']['amount']) {
                $bill->fireRefundEvent($payment);
            } else {
                $bill->fireRefundEvent($payment, $response['transaction']['amount']);
            }
        } else {
            $payment->results = $response;
            $payment->status = false;
            $payment->brand = $brand;
            $payment->card_number = $card_number;
            $payment->bank_transaction_id = $bank_transaction_id;
            $payment->bank_message = $bank_message;
            $payment->webhook_response_received = true;
            $payment->save();
        }

        return true;
    }

    /*
    * Check signature is correct
    *
    */
    private function checkMastercardSignature($request)
    {
        if ($request->header('X-Notification-Secret') == config('payment.drivers.mastercard_iframe.X-Notification-Secret')) {
            return true;
        }

        return false;
    }
}
