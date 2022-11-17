<?php

namespace App\Jobs;

use App\Mail\TransferBillsExportedExcelMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Mail;

class SendExportedTransferBillsMailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $file_name;
    protected $email;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file_name, $email)
    {
        $this->file_name = $file_name;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = (new TransferBillsExportedExcelMail($this->file_name));
        Mail::to($this->email)->send($message);
    }
}