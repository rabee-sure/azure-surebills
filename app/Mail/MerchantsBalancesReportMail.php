<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class MerchantsBalancesReportMail extends Mailable
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
        
        return $this->subject("Merchant transfer need fix")
            ->view('emails.reports.merchant_balance_transfer')
            ->attach(storage_path($reportFileName));
    }
}
