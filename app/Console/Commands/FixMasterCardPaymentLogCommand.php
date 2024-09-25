<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\PaymentLog;
use App\Services\MasterCardService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FixMasterCardPaymentLogCommand extends Command
{
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
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, config('payment.drivers.mastercard_iframe.api_base_url') . '/order/' . $billId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Basic " . base64_encode(config('payment.drivers.mastercard_iframe.operator_username') . ':' . config('payment.drivers.mastercard_iframe.operator_password')),
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }

        return false;
    }
}
