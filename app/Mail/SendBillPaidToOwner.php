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
        \App::setLocale($this->bill->user->settings->default_lang); 
        return $this->subject(__("You've got a payment! :total SAR from :name", [
            "total" => $this->bill->total,
            "name" => $this->bill->customer_name,
        ]))->view('emails.bills.paid_to_owner');
    }
}
