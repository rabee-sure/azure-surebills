<?php

namespace App\Jobs;

use App\Exports\ActiveAdminsExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ExportActiveAdminsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @return void
     */
    public function handle()
    {
        Log::info('Active admins report generation started');

        $disk = config('active_admins_report.disk');
        $directory = trim(config('active_admins_report.directory'), '/');
        $fileName = 'active_admins_report_'.Str::uuid()->toString().'.xlsx';
        $filePath = $directory.'/'.$fileName;

        try {
            // Generate the workbook inside this job (not via Maatwebsite queued export)
            // so the workflow stays two jobs: export then email.
            (new ActiveAdminsExport())->store($filePath, $disk);

            Log::info('Active admins report generated', [
                'file' => $filePath,
            ]);
        } catch (\Throwable $exception) {
            Log::error('Active admins report failed', [
                'stage' => 'export',
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        SendActiveAdminsReportJob::dispatch($filePath);
    }

    /**
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error('Active admins report failed', [
            'stage' => 'export',
            'message' => $exception->getMessage(),
        ]);
    }
}
