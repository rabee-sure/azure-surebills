<?php

namespace App\Jobs;

use App\Mail\SendTransferToCustomer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendCompletedTransferEmailToMerchant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transfer;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transfer)
    {
        $this->transfer = $transfer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = (new SendTransferToCustomer($this->transfer))->onQueue(config('queue.working_queues.email_queue'));
        Mail::to($this->transfer->user->email)->queue($message);
    }
}