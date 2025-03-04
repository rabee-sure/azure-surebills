<?php

namespace App\Console\Commands;

use App\Services\CyberSourceWebhookService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CybersourceWebhookSubscribeAllProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cybersource:webhook-subscribe-all-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cybersource webhook subscribe all products';

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
        $productsList = $this->cyberSourceWebhookService->findProductsToSubscribe()[0] ?? [];
        foreach($productsList as $productList)
        {
            $eventNames = array_map(function ($item) {
                return $item['eventName'];
            }, $productList['eventTypes']);
            
            $this->cyberSourceWebhookService->createWebhookSubscription($productList['productId'], $eventNames);
        }
        
        $this->info('success');
        return 0;
    }
}
