<?php

namespace App\Abstracts;

use App\Jobs\SendAlertMailForDuplicationRefund;
use App\Models\PaymentLog;
use App\Models\Transaction;

abstract class PaymentAbstract
{
    protected $providerName;

    public function __construct($providerName) {
        $this->providerName = $providerName;
    }

    abstract public function processPayment($bill, $cardDetails);
    abstract public function processRefund($transactionId, $amount, $code);
    abstract protected function preparePaymentPayload($bill, $cardDetails);
    
    protected function createPaymentLog($billId, $paymentMethod){
        return PaymentLog::create([
            'bill_id'        => $billId,
            'payment_method' => $paymentMethod,
            'results'        => [],
            'data'           => [],
            'status'         => 0,
            'provider_name' => $this->providerName,
        ]);
    }

    protected function updatePaymentLog($paymentLog, $response, $paymentStatus = false){
        $paymentLog->status = $paymentStatus;
        $paymentLog->bank_transaction_id = $response['id'] ?? null;
        $paymentLog->bank_message = $response['bank_message'] ?? null;
        $paymentLog->results = $response;
        $paymentLog->provider_name = $this->providerName;
        $paymentLog->save();
        return $paymentLog->fresh();
    }

    protected function updateBillStatus($bill, $paymentStatus, $type){
        if($paymentStatus){
            if($type == 'payment'){
                $bill->setPaid();
            } else if($type == 'refund'){
                $bill->setRefunded();
            }
        } else {
            $bill->status = 'pending';
            $bill->save();
        }
        return $bill->fresh();
    }

    protected function completeCycle($response, $fullResponse, $bill, $payment){
        // data
        $bank_message = $response['bank_message'] ?? null;
        $bank_transaction_id = $response['bank_transaction_id'] ?? null;
        $brand = $response['brand'];
        $card_number = $response['card_number'];

        $payment->results = $fullResponse;
        // $payment->mastercard_after_webhook_response = $fullResponse;
        $payment->brand = $brand;
        $payment->card_number = $card_number;
        $payment->bank_transaction_id = $bank_transaction_id;
        $payment->bank_message = $bank_message;
        $payment->status = $response['status'] ? true : false;
        $payment->webhook_response_received = ($response['type'] == 'payment') ?? true;
        $payment->save();
        
        $transaction = [];
        $transaction['status'] = $response['status'] ? true : false;
        $transaction['bill'] = $bill;
        $transaction['payment'] = $payment;
        
        if($response['type'] == 'payment'){
            $cycle = $this->handlePaymentTransaction($transaction);
        }elseif($response['type'] == 'refund'){
            $transaction['amount'] = $response['amount'];

            $cycle = $this->handleRefundTransaction($transaction);
        }

        return $cycle;
    }

    protected function handlePaymentTransaction($transaction)
    {
        if ($transaction['status']) {
            $transaction['bill']->setPaid();
            $transaction['bill']->firePaidEvent($transaction['payment']);
        } else {
            // set failed if coming from the application
            if($transaction['bill']->application && $transaction['bill']->user->settings->api_bill_style && $transaction['bill']->is_redirect) {
                $transaction['bill']->status = 'failed';
                $transaction['bill']->save();
            }
        }

        return true;
    }

    public function handleRefundTransaction($transaction)
    {
        if ($transaction['status']) {
            $duplicated_refund_transaction = Transaction::where('amount', $transaction['amount'])->where('bill_id', $transaction['bill']->id)->where('transaction_source', 'refund')->first();
            if($duplicated_refund_transaction){
                SendAlertMailForDuplicationRefund::dispatch($duplicated_refund_transaction->bill_id);
            }

            if ($transaction['bill']->total == $transaction['amount']) {
                $transaction['bill']->fireRefundEvent($transaction['payment']);
            } else {
                $transaction['bill']->fireRefundEvent($transaction['payment'], $transaction['amount']);
            }
        }

        return true;
    }

    
}
