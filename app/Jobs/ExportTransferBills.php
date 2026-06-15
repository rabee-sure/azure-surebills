<?php

namespace App\Jobs;

use App\Exports\TransferBillsExportData;
use App\Support\Storage\ExportStoragePaths;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ExportTransferBills implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $transfer;
    public $email;
    public $queue;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transfer, $email)
    {
        $this->transfer = $transfer;
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
        $exportRoot = ExportStoragePaths::transferBillsExportsRoot();
        $relativePath = $exportRoot.'/'.$file_name;

        Storage::disk('public')->makeDirectory($exportRoot);

        return (new TransferBillsExportData($this->transfer))
            ->store($relativePath, 'public')
            ->allOnQueue($this->queue)
            ->chain([
                (new SendExportedTransferBillsMailsJob($relativePath, $this->email))->onQueue($this->queue),
            ]);
    }
}