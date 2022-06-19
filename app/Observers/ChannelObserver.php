<?php

namespace App\Observers;

use App\Models\Channel;
use App\Events\AddActionLogEvent;
use Illuminate\Support\Facades\Auth;

class ChannelObserver
{
    /**
     * Handle the Channel "created" event.
     *
     * @param  \App\Models\Channel  $channel
     * @return void
     */
    public function created(Channel $channel)
    {
        event(new AddActionLogEvent(
            'create_channel',
            Auth::id(),
            [
                'message' => [
                    'username' => $channel->user->name,
                    'adminname' => Auth::user()->name,
                    'time' => $channel->created_at,
                ],
                'changes' => [],
            ],
            $channel->id,
            Channel::class
        ));
    }

    /**
     * Handle the Channel "updated" event.
     *
     * @param  \App\Models\Channel  $channel
     * @return void
     */
    public function updated(Channel $channel)
    {
        $fields = config('channelfields');

        $fieldsChanges = [];
        foreach($fields as $field){
            if($channel->isDirty($field)){
                $fieldsChanges[$field] = [
                    'old_value' => $channel->getOriginal($field),
                    'new_value' => $channel->$field
                ];
            }
        }
        event(new AddActionLogEvent(
            'update_channel', 
            Auth::id(), 
            [
                'message' => [
                    'username' => $channel->user->name,
                    'adminname' => Auth::user()->name,
                    'time' => $channel->updated_at,
                ],
                'changes' => $fieldsChanges,
            ], 
            $channel->id, 
            Channel::class
        ));
    }

    /**
     * Handle the Channel "deleted" event.
     *
     * @param  \App\Models\Channel  $channel
     * @return void
     */
    public function deleted(Channel $channel)
    {
        event(new AddActionLogEvent(
            'delete_channel',
            Auth::id(),
            [
                'message' => [
                    'username' => $channel->user->name,
                    'adminname' => Auth::user()->name,
                    'time' => $channel->created_at,
                ],
                'changes' => [],
            ],
            $channel->id,
            Channel::class
        ));
    }

    /**
     * Handle the Channel "restored" event.
     *
     * @param  \App\Models\Channel  $channel
     * @return void
     */
    public function restored(Channel $channel)
    {
        //
    }

    /**
     * Handle the Channel "force deleted" event.
     *
     * @param  \App\Models\Channel  $channel
     * @return void
     */
    public function forceDeleted(Channel $channel)
    {
        //
    }
}
