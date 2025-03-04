<?php

namespace App\Console\Commands;

use App\Services\CyberSourceWebhookService;
use Illuminate\Console\Command;

class CybersourceGetAllWebhookSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cybersource:get-all-webhook-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for get all webhook subscriptions for organization';

    protected $cyberSourceWebhookService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CyberSourceWebhookService $cyberSourceWebhookService)
    {
        $this->cyberSourceWebhookService = $cyberSourceWebhookService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $organizationId = config('cybersource.merchant_id');
        $products = config('cybersource.products');

        foreach ($products as $product) {
            if(count($product['eventTypes']) > 0) {
                foreach ($product['eventTypes'] as $eventType) {
                    $result = $this->cyberSourceWebhookService->getWebhookSubscriptionsByOrg($organizationId, $product['productId'], $eventType);
                    print_r($result[0]);
                }
            }else{
                $result = $this->cyberSourceWebhookService->getWebhookSubscriptionsByOrg($organizationId, $product['productId']);
                print_r($result[0]);
            }
        }
    }
}
