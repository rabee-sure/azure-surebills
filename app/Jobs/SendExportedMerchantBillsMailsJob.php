<?php

namespace App\Jobs;

use App\Mail\BillsExportedExcelMail;
use App\Mail\MerchantBillsExportedExcelMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Mail;

class SendExportedMerchantBillsMailsJob implements ShouldQueue
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
        \Log::channel('export_queue')->info("start handle function in SendExportedMerchantBillsMailsJob Job");
        $message = (new MerchantBillsExportedExcelMail($this->file_name));
        \Log::channel('export_queue')->info("send mail to : ".$this->email);
        Mail::to($this->email)->send($message);
        \Log::channel('export_queue')->info("end handle function in SendExportedMerchantBillsMailsJob Job");
    }
}
