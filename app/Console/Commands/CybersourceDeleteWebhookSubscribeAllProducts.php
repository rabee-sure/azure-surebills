<?php

namespace App\Console\Commands;

use App\Services\CyberSourceWebhookService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CybersourceDeleteWebhookSubscribeAllProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cybersource:delete-webhook-subscribe-all-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Cybersource webhook subscribe all products';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    protected $cyberSourceWebhookService;
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
        $subscribedProducts = $this->cyberSourceWebhookService->getWebhookSubscriptionsByOrg(config('cybersource.merchant_id'), '', '')[0] ?? [];
        foreach($subscribedProducts as $subscribedProduct)
        {
            $this->cyberSourceWebhookService->deleteWebhookSubscription($subscribedProduct['webhookId']);
        }
    }
}
