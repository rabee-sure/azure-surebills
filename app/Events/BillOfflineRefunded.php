<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BillOfflineRefunded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bill;
    public $total_remain;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($bill, $total_remain)
    {
        $this->bill = $bill;
        $this->total_remain = $total_remain;
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
