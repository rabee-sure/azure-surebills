<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\PaymentLog;
use App\Services\MasterCardService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class CheckMastercardWebhookForBillTransactionCommand extends Command
{
    private $client, $url, $headers;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:check-mastercard-webhook-for-bill-transaction {bill_id} {transaction_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check mastercard webhook for bill_id and transaction_id';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $bill_id = $this->argument('bill_id');
        $transaction_id = $this->argument('transaction_id');
        $bill = Bill::find($bill_id);
        $payment = PaymentLog::find($transaction_id);
        
        $masterCardService = new MasterCardService;
        
        $masterCardResponse = $masterCardService->getBillTransactionStatusFromMasterCard($bill_id, $transaction_id);
        
        $jsonOutput = json_encode($masterCardResponse, JSON_PRETTY_PRINT);

        // Print to console
        $this->info('Mastercard Response : '.$jsonOutput);

        if($this->confirm('Do you want to recall webhook for this bill')){
            if ($masterCardResponse) {
                $masterCardResponseTransactions = $masterCardResponse['transaction'];
                $filteredMasterCardResponseTransaction = array_filter($masterCardResponseTransactions, function ($transaction) use ($bill){
                    if($bill->status == 'paid')
                    {
                        return $transaction['transaction']['type'] === 'PAYMENT' && $transaction['result'] == 'SUCCESS';;
                    }
                    else if($bill->status == 'refunded')
                    {
                        return $transaction['transaction']['type'] === 'REFUND' && $transaction['result'] == 'SUCCESS';;
                    }
                });
                $filteredMasterCardResponseTransaction = reset($filteredMasterCardResponseTransaction);
                if (isset($filteredMasterCardResponseTransaction['order']) && isset($filteredMasterCardResponseTransaction['order']['id']) && isset($filteredMasterCardResponseTransaction['transaction']) && isset($filteredMasterCardResponseTransaction['transaction']['id']) && isset($filteredMasterCardResponseTransaction['transaction']['type'])) {
                    $this->info('master_card : master card order id = '. $filteredMasterCardResponseTransaction['order']['id']);
                    $payment = PaymentLog::find($filteredMasterCardResponseTransaction['transaction']['id']);
                    if ($filteredMasterCardResponseTransaction['transaction']['type'] == "PAYMENT") {
                        $masterCardService->handlePaymentTransaction($filteredMasterCardResponseTransaction, $bill, $payment);
                    } else if ($filteredMasterCardResponseTransaction['transaction']['type'] == "REFUND") {
                        $masterCardService->handleRefundTransaction($filteredMasterCardResponseTransaction, $bill, $payment);
                    } else {
                        $this->info('master_card : Faild to handle = ' . $bill->id);
                    }
                }
            } else {
                $this->info('master_card : no response from master card = ' . $bill->id);
            }
        }


        return 0;
    }
}