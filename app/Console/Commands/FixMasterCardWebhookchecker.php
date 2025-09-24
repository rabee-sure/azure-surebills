<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\PaymentLog;
use App\Services\MasterCardService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class FixMasterCardWebhookchecker extends Command
{
    private $client, $url, $headers;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:mastercard-webhookchecker {start_date} {end_date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check MasterCard Webhook for paid bills';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->client = new Client();
        $this->url = config('payment.drivers.mastercard_iframe.api_base_url') . '/order/';
        $this->headers = [
            'Authorization' => 'Basic ' . base64_encode(config('payment.drivers.mastercard_iframe.operator_username') . ':' . config('payment.drivers.mastercard_iframe.operator_password')),
            'Content-Type' => 'application/json',
        ];

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::error('This is start of FixMasterCardPaymentLogCommand');
        $start_date = $this->argument('start_date');
        $end_date = $this->argument('end_date');
        Log::error('start date = '.$start_date);
        Log::error('end date = '.$end_date);
        $this->info('paid bills will check from '.$start_date.' to '.$end_date);
        $masterCardService = new MasterCardService;
        $loop = 1;
        $paidBills = Bill::whereDoesntHave('transactions')
            ->whereDate('paid_at', '>=', $start_date)
            ->whereDate('paid_at', '<=', $end_date)
            ->whereIn('status', ['paid', 'refunded']);
        
        $this->info('paid bills count = '.$paidBills->count());

        if($this->confirm('Do you wish to check this bills ?')){
            $paidBills->chunk(10, function ($bills) use ($masterCardService, $loop) {
                    foreach ($bills as $bill) {
                        Log::channel('master_card')->error('DB bill = ' . $bill->id);
                        $this->line('Round '.$loop.' checking bill = '.$bill->id);
                        
                        $masterCardResponse = $this->getBillStatusFromMasterCard($bill->id);
                        if ($masterCardResponse) {
                            $this->line('- master card response found for bill = '.$bill->id);
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
                                Log::channel('master_card')->error('master card order id = ' . $filteredMasterCardResponseTransaction['order']['id']);
                                $payment = PaymentLog::find($filteredMasterCardResponseTransaction['transaction']['id']);
                                if ($filteredMasterCardResponseTransaction['transaction']['type'] == "PAYMENT") {
                                    $masterCardService->handlePaymentTransaction($filteredMasterCardResponseTransaction, $bill, $payment);
                                } else if ($filteredMasterCardResponseTransaction['transaction']['type'] == "REFUND") {
                                    $masterCardService->handleRefundTransaction($filteredMasterCardResponseTransaction, $bill, $payment);
                                } else {
                                    Log::channel('master_card')->error('Faild to handle = ' . $bill->id);
                                }
                            }
                        } else {
                            Log::channel('master_card')->error('no response from master card = ' . $bill->id);
                        }
                    }
    
                    $loop++;
                    sleep(3);
                });
    
            return 0;
        }
        
    }

    private function getBillStatusFromMasterCard($billId)
    {
        $response = $this->client->get($this->url . $billId, ['headers' => $this->headers]);
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return json_decode($response->getBody(), true);
        }

        return false;
    }
}