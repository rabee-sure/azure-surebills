<?php

namespace App\Console\Commands;

use App\Models\PaymentLog;
use App\Services\CyberSourceService;
use Illuminate\Console\Command;

class CybersourceGetTransactionDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cybersource:get-transaction-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for getting transaction details';

    protected $cyberSourceService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CyberSourceService $cyberSourceService)
    {
        $this->cyberSourceService = $cyberSourceService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (config('cybersource.transaction_checker_active')) {
            $period = config('cybersource.transaction_checker_period');
            switch ($period['unit']) {
                case 'minute':
                    $date = now()->subMinutes($period['value']);
                    break;
                case 'hour':
                    $date = now()->subHours($period['value']);
                    break;
                case 'day':
                    $date = now()->subDays($period['value']);
                    break;
                case 'week':
                    $date = now()->subWeeks($period['value']);
                    break;
                case 'month':
                    $date = now()->subMonths($period['value']);
                    break;
                case 'year':
                    $date = now()->subYears($period['value']);
                    break;
                default:
                    $date = now()->subDay();
                    break;
            }

            $date = date('Y-m-d H:i:s', strtotime($date));
            $this->info('STARTING DATE: ' . $date);

            $paymentLogsQuery = PaymentLog::where('webhook_response_received', true)
                ->where('is_failure', false)
                ->where('updated_at', '>=', $date)
                ->where('payment_method', '!=', 'mastercard_auth')
                ->where('status', true)
                ->where('provider_name', 'cybersource')
                ->whereNotNull('bank_transaction_id')
                ->select('bank_transaction_id')
                ->orderBy('updated_at', 'desc');

            $paymentLogsCount = clone $paymentLogsQuery;
            $paymentLogsRows = clone $paymentLogsQuery;
            $this->info('TRANSACTIONS: ' . $paymentLogsCount->count());

            $paymentLogsRows->chunk(10, function ($paymentLogs) {
                foreach ($paymentLogs as $paymentLogTransaction) {
                    $this->info('TRANSACTION ID: ' . $paymentLogTransaction);
                    if ($paymentLogTransaction == null) {
                        continue;
                    }
                    $transactionDetails = $this->cyberSourceService->checkTransaction($paymentLogTransaction->bank_transaction_id);
                    $completePaymentCycle = $this->cyberSourceService->createRevirseTransaction($transactionDetails);
                    if ($completePaymentCycle['status']) {
                        $this->info($completePaymentCycle['type'].' TRANSACTION SUCCESS');
                    } else {
                        $this->error($completePaymentCycle['type'].' TRANSACTION REVERSED');
                    }
                }
                sleep(10);
            });
        }

        return 0;
    }
}
