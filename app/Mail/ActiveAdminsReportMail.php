<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ActiveAdminsReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var string
     */
    public $filePath;

    /**
     * @param  string  $filePath
     * @return void
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @return $this
     */
    public function build()
    {
        $disk = config('active_admins_report.disk');
        $absolutePath = Storage::disk($disk)->path($this->filePath);

        return $this->subject('Active Admins Report')
            ->view('emails.reports.exported_active_admins')
            ->attach($absolutePath, [
                'as' => basename($this->filePath),
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
    }
}
