<?php

namespace App\Jobs;

use App\Mail\RequestReportMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Mail;

class SendMerchantOutstandingRepotEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $report;
    protected $report_emails;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($report,$report_emails)
    {
        $this->report = $report;
        $this->report_emails = $report_emails;
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $emails = explode(",", $this->report_emails);
        if(count($emails)){
            foreach ($emails as $email) {
                $message = (new RequestReportMail($this->report))->onQueue(config('queue.working_queues.export_queue'));
                Mail::to($email)->queue($message);
            }
        }
    }
}
