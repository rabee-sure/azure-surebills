<?php

namespace App\Jobs;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateReportExcelFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $report_id;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($report_id)
    {
        $this->report_id = $report_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        \Artisan::call('report:excel', [
            'id' => $this->report_id 
        ]);
    }
}
