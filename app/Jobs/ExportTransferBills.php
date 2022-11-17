<?php

namespace App\Jobs;

use App\Exports\TransferBillsExportData;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ExportTransferBills implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $transfer;
    public $email;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transfer, $email)
    {
        $this->transfer = $transfer;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file_name = 'bills_'.Carbon::now()->timestamp.'.xlsx';
        return (new TransferBillsExportData($this->transfer))
        ->store($filePath = 'transfer-bills/'. $file_name)
        ->chain([
            (new SendExportedTransferBillsMailsJob($file_name, $this->email))
        ]);

    }
}