<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class RequestTransferMail extends Mailable
{
    use Queueable, SerializesModels, IsMonitored;

    protected $date;

    protected $user;

    protected $transfer;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($date, $user, $transfer)
    {
        $this->date = $date;
        $this->user = $transfer->user;
        $this->transfer = $transfer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $transactionsFilePath = $this->transfer->filters['files']['file_path'];
        $transactionsFileName = basename($transactionsFilePath);
        $fileContent = Storage::get($transactionsFilePath);

        return $this->subject( $this->user->business_name ." requesting a new transfer - SureBills Transfers")
            ->view('emails.bills.request_transfer', [
                'user' => $this->user,
                'transfer' => $this->transfer,
            ])
            ->attachData($fileContent, $transactionsFileName);
    }

}
