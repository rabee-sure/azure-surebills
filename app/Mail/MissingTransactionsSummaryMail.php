<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class MissingTransactionsSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

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
        $fileContent = Storage::get($this->file_name);

        return $this->subject("Missing transactions inserted summary")
            ->view('emails.reports.missing_transactions_inserted_summary')
            ->attachData($fileContent, basename($this->file_name));
    }
}
