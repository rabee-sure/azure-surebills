<?php

namespace App\Jobs;

use App\Exports\BillMerchantExportData;
use App\Models\Bill;
use App\Models\RefundedBill;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

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
        \Log::channel('export_queue')->info("start handle function in ExportMerchantBills job");
        $file_name = 'bills_'.Carbon::now()->timestamp.'.xlsx';
        return (new BillMerchantExportData($this->filter))
        ->store($filePath = 'merchant-bills/'. $file_name)->allOnQueue($this->queue)
        ->chain([
            (new SendExportedMerchantBillsMailsJob($file_name, $this->email))->onQueue($this->queue)
        ]);
        \Log::channel('export_queue')->info("end handle function in ExportMerchantBills job");
    }
}
