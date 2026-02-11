<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\PaymentLog;

/**
 * Mastercard sandbox payment simulation.
 *
 * This is used only in non-production environments when the
 * MASTERCARD_PAYMENT_SIMULATION flag is enabled.
 * It generates fake MPGS-like responses so the internal
 * payment cycle can be tested without calling the gateway.
 */
class MasterCardSandboxSimulator
{
    /**
     * Simulate a full successful payment (3DS + PAY).
     *
     * @param  Bill       $bill
     * @param  PaymentLog $payment
     * @return array      Fake MPGS-like response
     */
    public function simulateSuccessfulPayment(Bill $bill, PaymentLog $payment): array
    {
        return [
            'result' => 'SUCCESS',
            'response' => [
                'gatewayCode' => 'APPROVED',
                'acquirerMessage' => 'APPROVED',
            ],
            'order' => [
                'id' => $bill->id,
                'amount' => $bill->total,
                'currency' => 'SAR',
                'status' => 'CAPTURED',
            ],
            'transaction' => [
                'id' => $payment->id,
                'type' => 'PAYMENT',
                'amount' => $bill->total,
                'currency' => 'SAR',
                'acquirer' => [
                    'transactionId' => 'SIMULATED-TXN-' . $payment->id,
                ],
            ],
            'sourceOfFunds' => [
                'provided' => [
                    'card' => [
                        'brand' => 'MASTERCARD',
                        'number' => '512345xxxxxx1234',
                    ],
                ],
            ],
        ];
    }

    /**
     * Simulate a successful refund transaction.
     *
     * @param  Bill       $bill
     * @param  PaymentLog $payment
     * @param  float      $amount
     * @return array      Fake MPGS-like refund response
     */
    public function simulateSuccessfulRefund(Bill $bill, PaymentLog $payment, float $amount): array
    {
        $amountFormatted = number_format($amount, 2, '.', '');

        return [
            'result' => 'SUCCESS',
            'response' => [
                'gatewayCode' => 'APPROVED',
                'acquirerMessage' => 'APPROVED',
            ],
            'order' => [
                'id' => $bill->id,
                'amount' => $bill->total,
                'totalRefundedAmount' => $amountFormatted,
                'currency' => 'SAR',
                'status' => 'CAPTURED',
            ],
            'transaction' => [
                'id' => $payment->id,
                'type' => 'REFUND',
                'amount' => $amountFormatted,
                'currency' => 'SAR',
                'acquirer' => [
                    'transactionId' => 'SIMULATED-REFUND-' . $payment->id,
                ],
            ],
            'sourceOfFunds' => [
                'provided' => [
                    'card' => [
                        'brand' => 'MASTERCARD',
                        'number' => '512345xxxxxx1234',
                    ],
                ],
            ],
        ];
    }
}