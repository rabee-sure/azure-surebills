<?php

namespace App\Console\Commands;

use App\Jobs\SendCompletedTransferEmailToMerchant;
use App\Models\Transfer;
use Illuminate\Console\Command;

class DispatchCompletedTransferEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:completed {transfer_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command fot test send completed transfer email job';

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
        $transfer_id = $this->argument('transfer_id');
        $transfer = Transfer::find($transfer_id);
        SendCompletedTransferEmailToMerchant::dispatch($transfer);
    }
}