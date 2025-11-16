<?php

namespace App\Console\Commands;

use App\Jobs\MastercardWebhookSimulation;
use App\Models\Bill;
use App\Models\PaymentLog;
use App\Services\MasterCardService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class ReviewMastercardTransactions extends Command
{
    private $client, $url, $headers;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mastercard:review-transactions {--from=} {--to=} {--manual}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Review mastercard transactions which not received webhook';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $from = $this->option('from');
        $to = $this->option('to');
        $manual = $this->option('manual') ?? false;

        $this->info('Check payment logs from '.$from.' to '.$to.' is processing...');
        Log::channel('mastercard_review_transactions_command')->error('Check payment logs from '.$from.' to '.$to.' is processing...');

        $paymentMethods = ['hyperpay_applepay', 'mastercard_applepay', 'mastercard_pay', 'mastercard_refund', 'stc_pay'];
        $paymentLogs = PaymentLog::whereIn('payment_method', $paymentMethods)->where('webhook_response_received', 0)->where('is_failure', 0);

        if($from){
            $paymentLogs = $paymentLogs->whereDate('created_at', '>=', $from);
        }
        if($to){
            $paymentLogs = $paymentLogs->whereDate('created_at', '<=', $to);
        }

        $paymentLogs = $paymentLogs->select('id', 'bill_id')->get()->toArray();
        $paymentLogsCount = count($paymentLogs);
        
        $this->line('Found '.$paymentLogsCount.' payment logs');
        Log::channel('mastercard_review_transactions_command')->error('Found '.$paymentLogsCount.' payment logs');

        if($paymentLogsCount > 0){
            // if option manual true ask confirmation if not proceed without confirmation
            if($manual){
                $this->info('Reviewing payment logs... with manual confirmation');
                Log::channel('mastercard_review_transactions_command')->error('Reviewing payment logs... with manual confirmation');
    
                if($this->confirm('Do you want to review these payment logs ?')){
                    $this->reviewPaymentLogs($paymentLogs);
                }
            }else{
                $this->info('Reviewing payment logs... without manual confirmation');
                Log::channel('mastercard_review_transactions_command')->error('Reviewing payment logs... without manual confirmation');
    
                $this->reviewPaymentLogs($paymentLogs);
            }
        }

        return 0;
    }

    private function reviewPaymentLogs($paymentLogsArr){
        $chunkedaymentLogs = array_chunk($paymentLogsArr, 10);
        $round = 1;
        foreach($chunkedaymentLogs as $paymentLogs){
            foreach($paymentLogs as$paymentLog){
                $this->line('Round '.$round.': Reviewing payment log = '.$paymentLog['id']);
                Log::channel('mastercard_review_transactions_command')->error('Round '.$round.': Reviewing payment log = '.$paymentLog['id']);

                MastercardWebhookSimulation::dispatch($paymentLog['bill_id'], $paymentLog['id']);
                $round++;
            }
        }
    }
}