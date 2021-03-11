<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class SendBillPaidToCustomer extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, IsMonitored;

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
        return $this->subject(__('Your bill of :total SAR has been successfully paid', ['total' => $this->bill->total]) )
                ->view('emails.bills.paid_to_customer');
    }
}
