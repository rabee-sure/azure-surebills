<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BillPaidReversed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bill;
    public $payment;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($bill, $payment)
    {
        $this->bill = $bill;
        $this->payment = $payment;
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
