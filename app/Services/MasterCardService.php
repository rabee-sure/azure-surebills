<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Bill;
use App\Models\Transfer;
use App\Models\PaymentLog;
use App\Models\Transaction;
use App\Models\TransferLog;
use Illuminate\Support\Facades\DB;
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
        if ($this->checkMastercardSignature($request)) {

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
                        $this->handlePaymentTransaction($response, $bill, $payment);
                    }

                    // handle REFUND transaction
                    if ($response['transaction']['type'] == "REFUND") {
                        $this->handleRefundTransaction($response, $bill, $payment);
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
        dump($response['response']['gatewayCode']);
        dump($response['result']);
        dd($response['transaction']['type']);
    }

    /*
    * handle REFUND transaction
    *
    */
    private function handleRefundTransaction($response, $bill, $payment)
    {
        dump($response['response']['gatewayCode']);
        dump($response['result']);
        dd($response['transaction']['type']);
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
