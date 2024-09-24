<?php

namespace App\Console\Commands;

use App\Models\Bill;
use Illuminate\Console\Command;

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
        \DB::enableQueryLog();
        $bills = Bill::whereHas('payment_logs', function($q){
            $q->where([['webhook_response_received', false], ['is_failure', false]]);
        })->whereDate('paid_at', '>=', '2024-09-18')->whereDate('paid_at', '<=', '2024-09-24')->select('id')->get();
        // ->toArray();
        dd(\DB::getQueryLog());
        dd($bills);

        // paidButNotHaveSuccessWebhook()->orderBy('created_at', 'DESC')->chunk(10, function($bills){
            foreach($bills as $bill)
            {
                $this->getBillStatusFromMasterCard($bill->id);
            }

            sleep(3);
        // });
    
        return 0;
    }

    private function getBillStatusFromMasterCard($billId)
    {
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, config('payment.drivers.mastercard_iframe.api_base_url').'/order/'.$billId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Basic ".base64_encode(config('payment.drivers.mastercard_iframe.operator_username').':'.config('payment.drivers.mastercard_iframe.operator_password')),
            "Content-Type: application/json"
        ]);
    
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        return json_decode($response, true);
    }
}
