<?php

namespace App\Mail;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Sent synchronously from {@see \App\Jobs\SendExportedMerchantBillsMailsJob} so the file is read and
 * attached in the same worker that already ran the export chain (no separate mail queue required).
 */
class MerchantBillsExportedExcelMail extends Mailable
{
    use SerializesModels;

    /** @var string Relative path on the public disk (includes OCI prefix when used) */
    public $export_storage_path;

    /** @var string Basename for the email view */
    public $file_name;

    /**
     * @param  string  $exportStoragePath  e.g. shared/exports/merchants/bills/bills_123.xlsx
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
        $binary = storage_read_public_disk_export_contents($this->export_storage_path);
        if ($binary === null) {
            throw new FileNotFoundException("Merchant bills export not found: {$this->export_storage_path}");
        }

        return $this->subject(__('Your Exported Bills - SureBills'))
            ->view('emails.bills.merchant_exported_bills', [
                'file_name' => $this->file_name,
            ])
            ->attachData($binary, $this->file_name, [
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
    }
}
