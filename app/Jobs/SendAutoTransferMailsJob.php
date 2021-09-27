<?php

namespace App\Jobs;

use App\Mail\AutoTransferMail;
use App\Models\PaymentLog;
use App\Models\Transaction;
use App\Models\Transfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Spatie\Valuestore\Valuestore;
use Illuminate\Support\Facades\Mail;

class SendAutoTransferMailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $day;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($day)
    {
        $this->day = $day;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $settings =  Valuestore::make(storage_path('app/settings.json'));
        $transfer_emails = $settings->get('transfer_emails');
        $emails = explode(",", $transfer_emails);
        if(count($emails)){
            foreach ($emails as $email) {
                Mail::to($email)->send(new AutoTransferMail($this->day));
            }
        }

    }
}
