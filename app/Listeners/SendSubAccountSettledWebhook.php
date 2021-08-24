<?php

namespace App\Listeners;

use App\Events\TransferCompleted;
use App\Http\Resources\WebhookTransferResource;
use App\Models\Channel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Spatie\WebhookServer\WebhookCall;

class SendSubAccountSettledWebhook
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
     * @param  TransferCompleted  $event
     * @return void
     */
    public function handle(TransferCompleted $event)
    {
        $transfer = $event->transfer;
        $channel_ids = $event->transfer->user->applications()
            ->whereHas('channel')
            ->get()
            ->pluck('channel_id')
            ->unique();

        $channels = Channel::whereIn('id', $channel_ids)->get();

        foreach ($channels as $channel) {
            if(isset($channel->sub_account_settled_webhook)){
                WebhookCall::create()
                   ->url($channel->sub_account_settled_webhook)
                   ->payload([
                        'type' => 'TransferCompleted',
                        'transfer' => new WebhookTransferResource($transfer),
                   ])
                   ->useSecret($channel->secret_token)
                   ->dispatch()
                   ->onQueue(env('WEBHOOK_QUEUE')); 
            }
        }
    }
}
