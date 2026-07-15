<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use Illuminate\Support\Facades\Storage;

class AutoTransferMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, IsMonitored;

    protected $day;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($day)
    {
        $this->day = $day;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $path = "automatic_transfers/" . date('Y-m-d', strtotime($this->day)) . "/master_sheet_" . date('Y-m-d', strtotime($this->day)) . ".zip";
        $fileName = basename($path);
        $fileContent = Storage::get($path);

        return $this->subject("SureBills Master Sheet $this->day")
            ->view('emails.bills.auto_transfer')
            ->attachData($fileContent, $fileName);
    }
}
