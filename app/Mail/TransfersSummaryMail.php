<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class TransfersSummaryMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, IsMonitored;

    protected $folder;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($folder)
    {
        $this->folder = $folder;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $dueAmountsPath = "summary_transfers/{$this->folder}/due_amounts.xlsx";
        $merchantsSummaryPath = "summary_transfers/{$this->folder}/merchants_summary.xlsx";

        $dueAmountsContent = Storage::get($dueAmountsPath);
        $merchantsSummaryContent = Storage::get($merchantsSummaryPath);

        return $this->subject("SureBills Master Sheet $this->folder")
            ->view('emails.bills.auto_transfer')
            ->attachData($dueAmountsContent, 'due_amounts.xlsx')
            ->attachData($merchantsSummaryContent, 'merchants_summary.xlsx');
    }
}
