<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class TransactionsExportedExcelMail extends Mailable
{
    use SerializesModels;

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
        $fileName = $this->file_name;
        $filePath = Storage::disk('local')->path(join(DIRECTORY_SEPARATOR, array('exported-transactions', $fileName)));
        return $this->subject("New Exported Transactions - SureBills")
            ->view('emails.transactions.exported_transactions', [
                'file_name' => $this->file_name,
            ])
            ->attach($filePath);
    }

}