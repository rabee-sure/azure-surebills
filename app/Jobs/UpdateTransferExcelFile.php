<?php

namespace App\Jobs;

use App\Models\Transfer;
use App\Models\PaymentLog;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateTransferExcelFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $transfer;
    protected $file_name;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Transfer $transfer, $file_name )
    {
        $this->transfer = $transfer;
        $this->file_name = $file_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->transfer->addMedia(storage_path('app/public/'.$this->file_name))
            ->preservingOriginal()
            ->toMediaCollection('transfers_transactions');
    }
}
