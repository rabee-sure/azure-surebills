<?php

namespace App\Listeners;

use App\Events\UserVerifiedChanged;
use App\Models\Channel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Spatie\WebhookServer\WebhookCall;

class SendSubAccountStatusWebhook
{
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
     * @param  UserVerifiedChanged  $event
     * @return void
     */
    public function handle(UserVerifiedChanged $event)
    {
        $channel_ids = $event->user->applications()
            ->whereHas('channel')
            ->get()
            ->pluck('channel_id')
            ->unique();
        $channels = Channel::whereIn('id', $channel_ids)->get();

        foreach ($channels as $channel) {
            if(isset($channel->sub_account_status_webhook)){
                WebhookCall::create()
                   ->url($channel->sub_account_status_webhook)
                   ->payload([
                        'type' => 'UserVerifiedChanged',
                        'user_verified' => $event->user->verified,
                   ])
                   ->useSecret($channel->secret_token)
                   ->dispatch()
                   ->onQueue(env('WEBHOOK_QUEUE')); 
            }
        }
    }
}
