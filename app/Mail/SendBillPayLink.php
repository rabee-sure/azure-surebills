<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendBillPayLink extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $bill;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($bill, $subject)
    {
        $this->bill = $bill;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        \App::setLocale($this->bill->user->settings->default_lang); 
        return $this->subject($this->subject)->view('emails.bills.payLink');
    }
}
