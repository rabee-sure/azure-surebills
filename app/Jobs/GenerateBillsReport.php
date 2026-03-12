<?php

namespace App\Jobs;

use App\Exports\ReportBillExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateBillsReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $filter;
    public $emails;
    public $report_name;
    public $report_id;
    public $queue;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($filter, $emails, $report_name, $report_id)
    {
        $this->filter = $filter;
        $this->emails = $emails;
        $this->report_name = $report_name;
        $this->report_id = $report_id;
        $this->queue = config('queue.working_queues.export_queue');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file_name = 'reports/'.$this->report_name.'/'.$this->report_name.'_'.$this->report_id.'.xlsx';
        return (new ReportBillExport($this->filter))
        ->store($filePath = $file_name, 'oci')->allOnQueue($this->queue)
        ->chain([
            (new SendExportedBillsReportMailsJob($this->emails, $this->report_id))->onQueue($this->queue),
            (new ActiveReportJob($this->report_id))->onQueue($this->queue)
        ]);
    }
}
