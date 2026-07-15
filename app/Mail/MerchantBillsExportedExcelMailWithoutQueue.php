<?php

namespace App\Mail;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class MerchantBillsExportedExcelMailWithoutQueue extends Mailable
{
    use SerializesModels, IsMonitored;

    public $export_storage_path;

    public $file_name;

    /**
     * @param  string  $exportStoragePath  Full relative path or legacy basename (see storage_read_public_disk_export_contents)
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
            throw new FileNotFoundException("Merchant bills export not found: {$this->export_storage_path}");
        }

        return $this->subject(__('Your Exported Bills - SureBills Without Queue'))
            ->view('emails.bills.merchant_exported_bills', [
                'file_name' => $this->file_name,
            ])
            ->attachData($binary, $this->file_name, [
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
    }
=======
        $fileName = $this->file_name;
        $fileContent = Storage::get('merchant-bills/' . $fileName);
        return $this->subject("Your Exported Bills - SureBills Without Queue")
            ->view('emails.bills.merchant_exported_bills', [
                'file_name' => $this->file_name,
            ])
            ->attachData($fileContent, $fileName, [
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
    }

>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
}
