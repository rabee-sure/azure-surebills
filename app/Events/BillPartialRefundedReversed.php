<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BillPartialRefundedReversed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bill;
    public $payment;
    public $amount;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($bill, $payment, $amount)
    {
        $this->bill = $bill;
        $this->payment = $payment;
        $this->amount = $amount;
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
