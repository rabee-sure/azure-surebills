<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

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
        $reportFileName = "app/public/".$this->file_name;
        
        return $this->subject("Missing transactions inserted summary")
            ->view('emails.reports.missing_transactions_inserted_summary')
            ->attach(storage_path($reportFileName));
    }
}
