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
<<<<<<< HEAD
        $timestamp = $this->date->timestamp;
        $formate = $this->date->format('l d/m/Y');
        $day = $this->date->format('d-m-Y');

        $path = $this->transfer->filters['files']['file_path'] ?? '';
        $attachmentName = basename($path);
=======
        $transactionsFilePath = $this->transfer->filters['files']['file_path'];
        $transactionsFileName = basename($transactionsFilePath);
        $fileContent = Storage::get($transactionsFilePath);
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4

        return $this->subject( $this->user->business_name ." requesting a new transfer - SureBills Transfers")
            ->view('emails.bills.request_transfer', [
                'user' => $this->user,
                'transfer' => $this->transfer,
            ])
<<<<<<< HEAD
            ->attachFromStorageDisk('public', $path, $attachmentName);
=======
            ->attachData($fileContent, $transactionsFileName);
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
    }

}
