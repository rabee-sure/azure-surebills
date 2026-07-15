<?php

namespace App\Mail;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

/**
 * Sent synchronously from {@see \App\Jobs\SendExportedTransferBillsMailsJob} so the export is read and
 * attached in the same worker as the Excel chain (no separate mail queue required).
 */
class TransferBillsExportedExcelMail extends Mailable
{
    use SerializesModels, IsMonitored;

    /** @var string Relative path on the public disk (includes OCI prefix when used) */
    public $export_storage_path;

    /** @var string Basename for the email view */
    public $file_name;

    /**
     * @param  string  $exportStoragePath  e.g. shared/exports/transfers/bills/bills_123.xlsx
     */
    public function __construct(string $exportStoragePath)
    {
        $this->export_storage_path = ltrim($exportStoragePath, '/');
        $this->file_name = basename($exportStoragePath);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
<<<<<<< HEAD
        $binary = storage_read_public_disk_export_contents($this->export_storage_path);
        if ($binary === null) {
            throw new FileNotFoundException("Transfer bills export not found: {$this->export_storage_path}");
        }

        return $this->subject(__('Your Exported Transfer Bills - SureBills'))
            ->view('emails.bills.transfer_exported_bills', [
                'file_name' => $this->file_name,
            ])
            ->attachData($binary, $this->file_name, [
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
    }
=======
        $fileName = $this->file_name;
        $fileContent = Storage::get('transfer-bills/' . $fileName);
        return $this->subject("Your Exported Transfer Bills - SureBills")
            ->view('emails.bills.transfer_exported_bills', [
                'file_name' => $this->file_name,
            ])
            ->attachData($fileContent, $fileName, [
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
    }

>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
}
