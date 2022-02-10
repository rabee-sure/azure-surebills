<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransferFileGenerated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transfer_emails;
    public $cycleDate;
    public $transfer;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($transfer_emails, $cycleDate, $transfer)
    {
        $this->transfer_emails = $transfer_emails;
        $this->cycleDate = $cycleDate;
        $this->transfer = $transfer;
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
