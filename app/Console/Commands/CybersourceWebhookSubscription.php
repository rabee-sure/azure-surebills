<?php

namespace App\Console\Commands;

use App\Services\CyberSourceWebhookService;
use Illuminate\Console\Command;

class CybersourceWebhookSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cybersource:webhook-subscription {productId} {eventTypes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for webhook subscription';

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
        $productId = $this->argument('productId');
        $eventTypes = explode(',', $this->argument('eventTypes'));
        $result = $this->cyberSourceWebhookService->createWebhookSubscription($productId, $eventTypes);
        //$this->info($result);
        return 1;
    }
}
