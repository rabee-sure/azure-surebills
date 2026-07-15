<?php

namespace App\Mail;

use App\Support\Storage\ExportStoragePaths;
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
<<<<<<< HEAD
        $dueAmountsFile = ExportStoragePaths::summaryTransferFolder($this->folder).'/due_amounts.xlsx';
        $merchantsSummaryFile = ExportStoragePaths::summaryTransferFolder($this->folder).'/merchants_summary.xlsx';

        return $this->subject("SureBills Master Sheet $this->folder")
            ->view('emails.bills.auto_transfer')
            ->attachFromStorageDisk('public', $dueAmountsFile, 'due_amounts.xlsx')
            ->attachFromStorageDisk('public', $merchantsSummaryFile, 'merchants_summary.xlsx');
=======
        $dueAmountsPath = "summary_transfers/{$this->folder}/due_amounts.xlsx";
        $merchantsSummaryPath = "summary_transfers/{$this->folder}/merchants_summary.xlsx";

        $dueAmountsContent = Storage::get($dueAmountsPath);
        $merchantsSummaryContent = Storage::get($merchantsSummaryPath);

        return $this->subject("SureBills Master Sheet $this->folder")
            ->view('emails.bills.auto_transfer')
            ->attachData($dueAmountsContent, 'due_amounts.xlsx')
            ->attachData($merchantsSummaryContent, 'merchants_summary.xlsx');
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
    }
}
