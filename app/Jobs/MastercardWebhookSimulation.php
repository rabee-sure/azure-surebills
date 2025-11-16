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
        $response = $this->masterCardService->getBillTransactionStatusFromMasterCard($this->billId, $this->transactionId);
        $this->masterCardService->handleMastercardChecker($response);
    }
}