<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendBillPaidToOwner extends Mailable
{
    use Queueable, SerializesModels;

    public $bill;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($bill)
    {
        $this->bill = $bill;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("You've got a payment! ".$this->bill->total." SAR from " . $this->bill->customer_name)->view('emails.bills.paid_to_owner');
    }
}
