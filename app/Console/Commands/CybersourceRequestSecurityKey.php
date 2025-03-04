<?php

namespace App\Console\Commands;

use App\Services\CyberSourceWebhookService;
use Illuminate\Console\Command;

class CybersourceRequestSecurityKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cybersource:request-security-key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for requesting security key';

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
        $result = $this->cyberSourceWebhookService->createWebhookSecurityKeys();
        dd($result);
    }
}
