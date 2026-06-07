<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject("New contact message from {$this->data['name']}")
            ->replyTo($this->data['email'], $this->data['name'])
            ->view('emails.contact.form_message', [
                'data' => $this->data,
            ]);
    }
}
