<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
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
        $fileName = $this->file_name;
        $filePath = Storage::disk('local')->path(join(DIRECTORY_SEPARATOR, array('shared-bills', $fileName)));
        return $this->subject("New Exported Bills - SureBills")
            ->view('emails.bills.exported_bills', [
                'file_name' => $this->file_name,
            ])
            ->attach($filePath);
    }

}