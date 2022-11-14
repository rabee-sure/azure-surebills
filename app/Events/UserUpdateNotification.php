<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserUpdateNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $oldData;
    public $updatedData;
    public $user_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($oldData, $updatedData, $user_id)
    {
        $this->oldData = $oldData;
        $this->updatedData = $updatedData;
        $this->user_id = $user_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
