<?php

namespace App\Listeners;

use App\Events\ContactSendEmail;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Mail;

class SendContactEmail
{
    public function handle(ContactSendEmail $event): void
    {
        Mail::to(config('app.contact_form_email'))
            ->send(new ContactFormMail($event->data));
    }
}
