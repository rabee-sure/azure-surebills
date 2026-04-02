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

class CreateTransferExcelFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $transfer_id;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transfer_id)
    {
        $this->transfer_id = $transfer_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        \Artisan::call('transfer:excel', [
            'id' => $this->transfer_id
        ]);
    }
}
