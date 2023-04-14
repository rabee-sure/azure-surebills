<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class RequestReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, IsMonitored;

    protected $report;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($report)
    {
        $this->report = $report;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::info('build email with attached file');
        // $reportFileName = "app/public/reports/{$this->report->name}/{$this->report->name}_{$this->report->id}.xlsx";

        if (Storage::exists("public/reports/{$this->report->name}/{$this->report->name}_{$this->report->id}.xlsx")) {
            $reportFileName = "app/public/reports/{$this->report->name}/{$this->report->name}_{$this->report->id}.xlsx";
        }

        if (Storage::exists("public/reports/{$this->report->name}/{$this->report->name}_{$this->report->id}_old.xlsx")) {
            $reportFileName = "app/public/reports/{$this->report->name}/{$this->report->name}_{$this->report->id}_old.xlsx";
        }
        
        return $this->subject( $this->report->name ." Report - SureBills Reports")
            ->view('emails.reports.request_report', [
                'report' => $this->report,
            ])
            ->attach(storage_path($reportFileName));
    }

}
