<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class MerchantBillsExportedExcelMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, IsMonitored;

    public $file_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($file_name)
    {
        $this->file_name = $file_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        \Log::channel('export_queue')->info("start build function in MerchantBillsExportedExcelMail Mail");
        $fileName = $this->file_name;
        $filePath = Storage::disk('local')->path(join(DIRECTORY_SEPARATOR, array('merchant-bills', $fileName)));
        \Log::channel('export_queue')->info("end build function in MerchantBillsExportedExcelMail Mail");
        return $this->subject("Your Exported Bills - SureBills")
            ->view('emails.bills.merchant_exported_bills', [
                'file_name' => $this->file_name,
            ])
            ->attach($filePath);
    }

}