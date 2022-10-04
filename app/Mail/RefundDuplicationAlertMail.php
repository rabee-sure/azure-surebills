<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RefundDuplicationAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bill_id;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($bill_id)
    {
        $this->bill_id = $bill_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Duplicated Refund Alert')
        ->view('emails.transactions.duplicated_refund_alert', [
            'bill_id' => $this->bill_id,
        ]);
    }
}
