<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AutoTransferMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

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
        $fileName = "app/public/automatic_transfers/".date('Y-m-d', strtotime($this->day))."/master_sheet_".date('Y-m-d', strtotime($this->day)).".zip";

        return $this->subject("SureBills Master Sheet $this->day")
            ->view('emails.bills.auto_transfer')
            ->attach(storage_path($fileName));
    }
}
