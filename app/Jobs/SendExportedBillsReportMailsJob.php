<?php

namespace App\Jobs;

use App\Mail\RequestReportMail;
use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendExportedBillsReportMailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $report_id;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $report_id)
    {
        $this->email = $email;
        $this->report_id = $report_id;
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $report = Report::findOrFail($this->report_id);

        $message = (new RequestReportMail($report));
        Mail::to($this->email)->send($message);
    }
}
