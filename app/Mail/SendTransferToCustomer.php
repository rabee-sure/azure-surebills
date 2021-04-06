<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\App;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class SendTransferToCustomer extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, IsMonitored;

    public $transfer;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($transfer)
    {
        $this->transfer = $transfer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        App::setLocale($this->transfer->user->settings->default_lang); 
        return $this->subject(__('An amount of :amount has been transferred to your bank account. Operation number #:number', [
            'amount' => $this->transfer->net_total,
            'number' => $this->transfer->id,
            ]) )
                ->view('emails.transfers.transfer_to_customer');
    }
}
