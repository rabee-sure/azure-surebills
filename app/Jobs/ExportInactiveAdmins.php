<?php

namespace App\Jobs;

use App\Exports\InactiveAdminsExport;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExportInactiveAdmins implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $email;
    private $date;
    // private $queue;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $date)
    {
        $this->email = $email;
        $this->date = $date;
        // $this->queue = config('queue.working_queues.export_queue');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file_name = 'Inactive_Admins _'.Carbon::now()->timestamp.'.xlsx';
        return (new InactiveAdminsExport($this->date))
        ->store($filePath = 'nova_reports/'. $file_name)->allOnQueue($this->queue)
        ->chain([
            (new SendExportedInactiveAdminsMailsJob($file_name, $this->email))->onQueue($this->queue)
        ]);
    }
}
