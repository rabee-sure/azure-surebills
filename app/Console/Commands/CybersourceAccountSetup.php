<?php

namespace App\Console\Commands;

use App\Services\CyberSourceWebhookService;
use Illuminate\Console\Command;

class CybersourceAccountSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cybersource:setup-account';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for setting up cybersource account';

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
        // Request a digital signature key.
        if($this->confirm('Do you want to request a digital signature key?')){
            $digitalSignatureKey = $this->cyberSourceWebhookService->createWebhookSecurityKeys();
            if($digitalSignatureKey){
                $this->info('Digital Signature Key created you can show it in : cybersource_create_webhook_security_keys_result.log');
            }else{
                $this->error('Digital Signature Key not created you can show exception in : cybersource_create_webhook_security_keys_faild.log');
            }
        }

        // Delete old webhooks.
        if($this->confirm('Do you want to delete old webhooks?')){
            $subscribedProducts = $this->cyberSourceWebhookService->getWebhookSubscriptionsByOrg(config('cybersource.merchant_id'), '', '')[0] ?? [];
            if($subscribedProducts){
                $this->info('You can show old webhooks will deleted in : cybersource_get_webhook_subscription_by_organization_result.log');
                
                if($this->confirm('Do you want to continue to delete old webhooks?')){
                    foreach($subscribedProducts as $subscribedProduct)
                    {
                        $this->cyberSourceWebhookService->deleteWebhookSubscription($subscribedProduct['webhookId']);
                        $this->line('Webhook deleted with id : '.$subscribedProduct['webhookId']);
                    }
                }else{
                    $this->error('Webhook deleting proccess cancelled.');
                }
            }else{
                $this->error('Get old webhooks have exception you can show it in : cybersource_get_webhook_subscription_by_organization_faild.log');
            }
        }

        // Subscribe to webhooks event notifications.
        if($this->confirm('Do you want to subscribe to webhooks event notifications?')){
            $choice = $this->choice('Which products do you want to subscribe?', ['API products', 'Config products'], 0);
            
            $productsList = [];
            if($choice == 'API products'){
                $productsList = $this->cyberSourceWebhookService->findProductsToSubscribe()[0] ?? [];
            }elseif($choice == 'Config products'){
                $productsList = config('cybersource.products') ?? [];
            }
            
            if($productsList){
                $this->info('You will subscribe to '.count($productsList).' products show details in : cybersource_find_products_to_subscribe_result.log');
                
                if($this->confirm('Do you want to continue to subscribe to webhooks event notifications?')){
                    foreach($productsList as $index => $productList)
                    {
                        if($choice == 'API products'){
                            $eventNames = array_map(function ($item) {
                                return $item['eventName'];
                            }, $productList['eventTypes']);
            
                            $subscribedProduct = $this->cyberSourceWebhookService->createWebhookSubscription($productList['productId'], $eventNames);
                        }elseif($choice == 'Config products'){
                            $subscribedProduct = $this->cyberSourceWebhookService->createWebhookSubscription($productList['productId'], $productList['eventTypes']);
                        }
                        
                        if($subscribedProduct){
                            $this->line(($index+1).'- Webhook subscribed with id : '.$productList['productId']);
                        }else{
                            $this->error(($index+1).'- '.$productList['productId'].' Webhook subscription failed.');
                        }
                    }
                }else{
                    $this->error('Webhook subscribing proccess cancelled.');
                }
            }else{
                $this->error('Products list have exception you can show it in : cybersource_find_products_to_subscribe_faild.log');
            }
        }

        $this->info('Cybersource account setup completed.');

        return 0;
    }
}
