<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddActionLogEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $action_name;
    public $user_id;
    public $payload;
    public $modelId;
    public $modelClass;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($action_name, $user_id, $payload, $modelId = null, $modelClass = null)
    {
        $this->action_name = $action_name;
        $this->user_id = $user_id;
        $this->payload = $payload;
        $this->modelClass = $modelClass;
        $this->modelId = $modelId;
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
