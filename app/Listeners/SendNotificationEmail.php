<?php

namespace App\Listeners;

use App\Events\UserUpdateNotification;
use App\Mail\SendUpdatedUserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class SendNotificationEmail implements ShouldQueue
{
    use IsMonitored;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\UserUpdateNotification  $event
     * @return void
     */
    public function handle(UserUpdateNotification $event)
    {
        $data['changes'] = '';
        $data['user'] = $event->user_id;
        $dirtyFields = $this->gitDiff($event->oldData, $event->updatedData);
        foreach($dirtyFields as $field){
            $data['changes'] .= $field.' change from '.$event->oldData[$field].' to '.$event->updatedData[$field].'/n';
        }
        
        $message = (new SendUpdatedUserNotification($data))->onQueue(env('EMAILS_QUEUE'));
        Mail::to('mzain@sure.com.sa')->queue($message);
    }

    private function gitDiff($old, $updated){
        return array_keys(array_diff($old, $updated));
    }
}
