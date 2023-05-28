<?php

namespace App\Jobs;

use App\Exports\VerifiedMerchantExportData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class ExportVerifiedMerchantsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $queue;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->queue = config('queue.working_queues.export_queue');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file_name = 'verified_merchants.xlsx';
        return (new VerifiedMerchantExportData())->store($filePath = 'verified-merchants/'. $file_name)->allOnQueue($this->queue);
    }
}
