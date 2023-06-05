<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class MercahntsReportsExportedExcelMail extends Mailable implements ShouldQueue
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
        $fileName = $this->file_name;
        $filePath = Storage::disk('local')->path(join(DIRECTORY_SEPARATOR, array('merchants_reports', $fileName)));
        return $this->subject("New Exported Merchants Report - SureBills")
            ->view('emails.reports.exported_merchants', [
                'file_name' => $this->file_name,
            ])
            ->attach($filePath);
    }

}