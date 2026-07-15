<?php

namespace App\Jobs;

use App\Models\Transfer;
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
<<<<<<< HEAD
        $this->transfer->addMediaFromDisk($this->file_name, 'public')
=======
        $this->transfer->addMedia($this->file_name)
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
            ->preservingOriginal()
            ->toMediaCollection('transfers_transactions');
    }
}
