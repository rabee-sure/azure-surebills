<?php

namespace App\Listeners;

use App\Events\TransferFileGenerated;
use App\Mail\RequestTransferMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendRequestTransferFile implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\TransferFileGenerated  $event
     * @return void
     */
    public function handle(TransferFileGenerated $event)
    {
        $emails = $this->parseEmails($event->transfer_emails);

        if (empty($emails)) {
            return;
        }

        $transfer = $event->transfer->loadMissing('user');

        foreach ($emails as $email) {
            Mail::to($email)->send(new RequestTransferMail($event->cycleDate, $transfer->user, $transfer));
        }
    }

    private function parseEmails($transferEmails): array
    {
        if ($transferEmails === null || $transferEmails === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $transferEmails))));
    }
}
