<?php

namespace App\Jobs;

use App\Models\Bill;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\WebhookServer\WebhookCall;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class CallbackWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    protected $bill;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Bill $bill)
    {
        $this->bill = $bill;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new \GuzzleHttp\Client([
            'http_errors' => false,
            'verify' => false
        ]);

        if (!$this->bill->webhook_url or !filter_var($this->bill->webhook_url, FILTER_VALIDATE_URL) or !isset(parse_url($this->bill->webhook_url)['host'])) {
            return true;
        }

        $request = $client->get($this->bill->webhook_url);
        $response = $request->getStatusCode();
        if($request->getStatusCode() == 200){
            $this->bill->is_callbacked = true;
            $this->bill->save();
        }
    }
}
