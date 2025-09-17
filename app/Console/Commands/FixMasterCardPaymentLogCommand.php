<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\PaymentLog;
use App\Services\MasterCardService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class FixMasterCardPaymentLogCommand extends Command
{
    private $client, $url, $headers;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:mastercard-payment-log {start_date} {end_date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix MasterCard Payment Log';

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
        Log::erro('This is start of FixMasterCardPaymentLogCommand');
        $start_date = $this->argument('start_date');
        $end_date = $this->argument('end_date');
        Log::erro('start date = '.$start_date);
        Log::erro('end date = '.$end_date);
        $masterCardService = new MasterCardService;
        Bill::whereDoesntHave('transactions')
            ->whereDate('paid_at', '>=', $start_date)
            ->whereDate('paid_at', '<=', $end_date)
            ->whereIn('status', ['paid', 'refunded'])
            ->chunk(10, function ($bills) use ($masterCardService) {
                foreach ($bills as $bill) {
                    Log::channel('master_card')->error('DB bill = ' . $bill->id);
                    $masterCardResponse = $this->getBillStatusFromMasterCard($bill->id);
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

                sleep(3);
            });

        return 0;
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