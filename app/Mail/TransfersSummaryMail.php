<?php

namespace App\Mail;

use App\Support\Storage\ExportStoragePaths;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TransfersSummaryMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

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
        $dueAmountsFile = ExportStoragePaths::summaryTransferFolder($this->folder).'/due_amounts.xlsx';
        $merchantsSummaryFile = ExportStoragePaths::summaryTransferFolder($this->folder).'/merchants_summary.xlsx';

        return $this->subject("SureBills Master Sheet $this->folder")
            ->view('emails.bills.auto_transfer')
            ->attachFromStorageDisk('public', $dueAmountsFile, 'due_amounts.xlsx')
            ->attachFromStorageDisk('public', $merchantsSummaryFile, 'merchants_summary.xlsx');
    }
}
