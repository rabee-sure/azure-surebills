<?php

namespace App\Console\Commands;

use App\Services\CyberSourceWebhookService;
use Illuminate\Console\Command;

class CybersourceWebhookProductList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cybersource:webhook-product-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for webhook product list';

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
        $result = $this->cyberSourceWebhookService->findProductsToSubscribe();
        dd($result);
    }
}
