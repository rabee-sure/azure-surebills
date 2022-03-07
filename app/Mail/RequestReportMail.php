<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class RequestReportMail extends Mailable
{
    use Queueable, SerializesModels, IsMonitored;

    protected $date;

    protected $user;

    protected $transfer;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($date, $user, $report)
    {
        $this->date = $date;
        $this->user = $user;
        $this->report = $report;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $timestamp = $this->date->timestamp;
        $formate = $this->date->format('l d/m/Y');
        $day = $this->date->format('d-m-Y');

        // $fileName = "app/bills/{$this->user->business_name_slug}/{$this->transfer->filters['files']['bills']}";
        $reportFileName = "app/public/reports/{$this->report->name}/{$this->report->name}_{$this->report->id}.xlsx";

        return $this->subject( $this->report->name ." Report - SureBills Reports")
            ->view('emails.reports.request_report', [
                'user' => $this->user,
                'transfer' => $this->transfer,
            ])
            // ->attach(storage_path($fileName))
            ->attach(storage_path($reportFileName));
    }

}
