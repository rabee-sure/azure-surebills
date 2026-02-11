<?php

namespace App\Jobs;

use App\Services\MasterCardService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MastercardWebhookSimulation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $billId;
    public $transactionId;
    public $masterCardService;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($billId, $transactionId)
    {
        $this->billId = $billId;
        $this->transactionId = $transactionId;
        $this->masterCardService = new MasterCardService();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // In full simulation mode we do not call MPGS at all.
        if (function_exists('mastercard_simulation_enabled') && mastercard_simulation_enabled()) {
            $payment = \App\Models\PaymentLog::find($this->transactionId);
            $bill = $payment ? $payment->bill : null;

            if ($bill && $payment) {
                $simulator = app(\App\Services\MasterCardSandboxSimulator::class);
                $fakeResponse = $simulator->simulateSuccessfulPayment($bill, $payment);
                $this->masterCardService->handlePaymentTransaction($fakeResponse, $bill, $payment);
            }

            return;
        }
        
        $response = $this->masterCardService->getBillTransactionStatusFromMasterCard($this->billId, $this->transactionId);
        $this->masterCardService->handleMastercardChecker($response);
    }
}