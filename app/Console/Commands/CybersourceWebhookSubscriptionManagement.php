<?php

namespace App\Console\Commands;

use App\Services\CyberSourceWebhookService;
use Illuminate\Console\Command;

class CybersourceWebhookSubscriptionManagement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cybersource:webhook-subscription-management {operation} {--webhookId=} {--organizationId=} {--productId=} {--eventType=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for webhook subscription management operations (getById , getByOrg, delete) webhookId option required with getById and delete operations, organizationId, productId, eventType requiired with getByOrg operation';

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
        $operation = $this->argument('operation');

        $webhookId = $this->option('webhookId') ?? null;
        $organizationId = $this->option('organizationId') ?? null;
        $productId = $this->option('productId') ?? null;
        $eventType = $this->option('eventType') ?? null;

        switch ($operation) {
            case 'getById':
                if(!$webhookId) {
                    $this->error('webhookId option is required');
                    return;
                }
                $result = $this->cyberSourceWebhookService->getWebhookSubscriptionById($webhookId);
                break;
            case 'getByOrg':
                if(!$organizationId) {
                    $this->error('organizationId option is required');
                    return;
                }
                if(!$productId) {
                    $this->error('productId option is required');
                    return;
                }
                if(!$eventType) {
                    $this->error('eventType option is required');
                    return;
                }
                $result = $this->cyberSourceWebhookService->getWebhookSubscriptionsByOrg($organizationId, $productId, $eventType);
                break;
            case 'delete':
                if(!$webhookId) {
                    $this->error('webhookId option is required');
                    return;
                }
                $result = $this->cyberSourceWebhookService->deleteWebhookSubscription($webhookId);
                break;
            default:
                $result = 'Invalid operation';
        }
        
        dd($result);
    }
}
