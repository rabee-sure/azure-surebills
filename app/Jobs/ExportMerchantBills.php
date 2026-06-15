<?php

namespace App\Jobs;

use App\Exports\BillMerchantExportData;
use App\Models\Bill;
use App\Models\RefundedBill;
use App\Support\Storage\ExportStoragePaths;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ExportMerchantBills implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $filter;
    public $email;
    public $queue;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filter, $email)
    {
        $this->filter = $filter;
        $this->email = $email;
        $this->queue = config('queue.working_queues.export_queue');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file_name = 'bills_'.Carbon::now()->timestamp.'.xlsx';
        $exportRoot = ExportStoragePaths::merchantBillsExportsRoot();
        $relativePath = $exportRoot.'/'.$file_name;

        Storage::disk('public')->makeDirectory($exportRoot);

        return (new BillMerchantExportData($this->filter))
            ->store($relativePath, 'public')
            ->allOnQueue($this->queue)
            ->chain([
                (new SendExportedMerchantBillsMailsJob($relativePath, $this->email))->onQueue($this->queue),
            ]);
    }
}
