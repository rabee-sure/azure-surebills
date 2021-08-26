<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Jobs\CallbackWebhook;
use Illuminate\Console\Command;
use Spatie\WebhookServer\WebhookCall;

class PaidWebhookMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:send-paid-webhook';

    /**
     * The console Send webhook for all paid bills.
     *
     * @var string
     */
    protected $description = 'Send webhook for all paid bills';

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
        $bills = Bill::paidButNotHaveSuccessWebhook()->get();

        $i = 0;
        foreach ($bills as $bill) {

            $i++;

            CallbackWebhook::dispatch($bill);

            WebhookCall::create()
                ->url($bill->application->webhook_url)
                ->payload([
                    'bill_number' => $bill->number,
                    'reference_id' => $bill->reference_id,
                    'status' => $bill->status,
                    'bill_id' => $bill->id,
                    'pay_url' => $bill->pay_url,
                    'total' => $bill->total,
                ])
                ->useSecret($bill->application->webhook_secret)
                ->dispatch()
                ->onQueue(env('WEBHOOK_QUEUE'));

            var_dump($i);
        }
    }
}
