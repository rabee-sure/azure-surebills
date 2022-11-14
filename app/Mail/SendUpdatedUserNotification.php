<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class SendUpdatedUserNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, IsMonitored;

    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        dd($this->data);
        return $this->subject('User Updated Notification '.$this->data['user'])
                ->view('emails.notifications.user_updated_notification');
    }
}
