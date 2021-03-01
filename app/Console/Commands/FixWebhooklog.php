<?php

namespace App\Console\Commands;

use App\Models\WebhookLog;
use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FixWebhooklog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:webhook_log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fix webhook log';

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
        $logs = WebhookLog::all();
        foreach ($logs as $log) {
            if($log->status_code == 200){
                $log->status = 1;
                $log->save();
                 $this->info("fix Webhook Log id: {$log->id}");
            }
        }
    }
}
