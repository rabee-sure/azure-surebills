<?php

namespace App\Jobs;

use App\Mail\RefundDuplicationAlertMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Mail;

class SendAlertMailForDuplicationRefund implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $bill_id;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bill_id)
    {
        $this->bill_id = $bill_id;
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = (new RefundDuplicationAlertMail($this->bill_id));
        Mail::to(['mzain@sure.com', 'abmostafa@surepay.sa'])->send($message);
    }
}
