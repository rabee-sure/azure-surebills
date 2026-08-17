<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendResetPassword extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $url;

    public $token;

    public $count;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($url, $token, $count)
    {
        $this->url = $url;
        $this->token = $token;
        $this->count = $count;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('Reset Password Notification'))->view('emails.auth.passwords.reset_password');
    }
}
