<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class InactiveAdminExportedExcelMail extends Mailable implements ShouldQueue
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
        $filePath = Storage::disk('local')->path(join(DIRECTORY_SEPARATOR, array('nova_reports', $fileName)));
        return $this->subject("New Exported Inactive Admin Report - SureBills")
            ->view('emails.reports.exported_inactive_admins', [
                'file_name' => $this->file_name,
            ])
            ->attach($filePath);
    }

}
