<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class BillsExportedExcelMail extends Mailable implements ShouldQueue
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
        $filePath = "app/public/shared-bills/{$this->file_name}";

        return $this->subject("New Exported Bills - SureBills")
            ->view('emails.bills.exported_bills', [
                'file_name' => $this->file_name,
            ])
            ->attach(storage_path($filePath));
    }

}
