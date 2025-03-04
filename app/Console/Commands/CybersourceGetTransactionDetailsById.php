<?php

namespace App\Console\Commands;

use App\Models\PaymentLog;
use App\Services\CyberSourceService;
use Illuminate\Console\Command;

class CybersourceGetTransactionDetailsById extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cybersource:get-transaction-details-by-id {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for getting transaction details by id';

    protected $cyberSourceService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CyberSourceService $cyberSourceService)
    {
        $this->cyberSourceService = $cyberSourceService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $result = $this->cyberSourceService->checkTransaction($this->argument('id'));
        if ($result['status']) {
            $this->info('TRANSACTION TRANSMITTED');
        } else {
            $this->error('TRANSACTION STILL PENDING');
        }

        return 0;
    }
}
