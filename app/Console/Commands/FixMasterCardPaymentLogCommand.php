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
    private $client, $url, $headers ;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:mastercard-payment-log';

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
        $masterCardService = new MasterCardService;
        Bill::whereDoesntHave('transactions')
            ->whereDate('paid_at', '>=', '2024-09-19')
            ->whereDate('paid_at', '<=', '2024-09-23')
            ->whereIn('status', ['paid', 'refunded'])
            ->chunk(10, function ($bills) use ($masterCardService) {
                foreach ($bills as $bill) {
                    Log::channel('master_card')->error('DB bill = ' . $bill->id);
                    $masterCardResponse = $this->getBillStatusFromMasterCard($bill->id);
                    if ($masterCardResponse) {
                        $masterCardResponse = $masterCardResponse['transaction'][0];
                        if (isset($masterCardResponse['order']) && isset($masterCardResponse['order']['id']) && isset($masterCardResponse['transaction']) && isset($masterCardResponse['transaction']['id']) && isset($masterCardResponse['transaction']['type'])) {
                            Log::channel('master_card')->error('master card order id = ' . $masterCardResponse['order']['id']);
                            $payment = PaymentLog::find($masterCardResponse['transaction']['id']);
                            if ($masterCardResponse['transaction']['type'] == "PAYMENT") {
                                $masterCardService->handlePaymentTransaction($masterCardResponse, $bill, $payment);
                            } else if ($masterCardResponse['transaction']['type'] == "REFUND") {
                                $masterCardService->handleRefundTransaction($masterCardResponse, $bill, $payment);
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
        $response = $this->client->get($this->url.$billId, ['headers' => $this->headers]);
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return json_decode($response->getBody(), true);
        }

        return false;
    }
}
