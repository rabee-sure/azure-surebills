<?php

namespace App\Jobs;

use App\Models\Bill;
use GuzzleHttp\Client;
use App\Models\PaymentLog;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class RefundMasterCardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    protected $bill;

    protected $log;

    protected $amount;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Bill $bill, PaymentLog $payment_log, $amount)
    {
        $this->bill   = $bill;
        $this->log    = $payment_log;
        $this->amount = $amount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // new log
        $payment = PaymentLog::create([
            'bill_id'        => $this->bill->id,
            'payment_method' => 'mastercard_refund',
            'results'        => [],
            'data'           => [],
            'status'         => 0,
        ]);

        // api link
        if ($this->log->created_at > '2021-02-04 03:05:33') {
            $link = config('payment.drivers.mastercard.base_url').'/api/rest/version/58/merchant/'.config('payment.drivers.mastercard.merchant_id').'/order/'.$this->bill->id.'/transaction/'.$payment->id;
        } else {
            $link = config('payment.drivers.mastercard.base_url').'/api/rest/version/58/merchant/'.config('payment.drivers.mastercard.merchant_id').'/order/'.$this->log->id.'/transaction/'.$payment->id;
        }

        $client = new Client(['http_errors' => false]);
        $response = $client->put($link,
            [
                'json' => [
                    'apiOperation' => 'REFUND',
                    'transaction' => [
                        'amount'   => number_format($this->amount, 2, '.', ''),
                        'currency' => 'SAR'
                    ]
                ],
                'auth' => [
                    config('payment.drivers.mastercard.operator_username'),
                    config('payment.drivers.mastercard.operator_password')
                ],
            ]
        );
        $response = json_decode($response->getBody()->getContents(), true);
        $payment->results = $response;
        $payment->refunded_amount = $this->amount;
        $payment->save();
    }
}
