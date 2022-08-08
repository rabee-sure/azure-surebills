<?php

namespace App\Jobs;

use App\Mail\AutoTransferMail;
use App\Mail\TransactionsExportedExcelMail;
use App\Models\PaymentLog;
use App\Models\Transaction;
use App\Models\Transfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Spatie\Valuestore\Valuestore;
use Illuminate\Support\Facades\Mail;

class SendExportedUserTranasctionsMailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $file_name;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file_name)
    {
        $this->file_name = $file_name;
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = (new TransactionsExportedExcelMail($this->file_name));
        Mail::to(['mzain@sure.com.sa'])->send($message);
    }
}
