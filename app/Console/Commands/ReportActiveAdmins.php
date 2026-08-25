<?php

namespace App\Console\Commands;

use App\Jobs\ExportActiveAdminsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReportActiveAdmins extends Command
{
    /**
     * @var string
     */
    protected $signature = 'admin:report_active';

    /**
     * @var string
     */
    protected $description = 'Dispatch the queued job that generates the active admins Excel report';

    /**
     * @return int
     */
    public function handle()
    {
        $email = config('active_admins_report.email');

        if (!$email) {
            Log::warning('Active admins report failed', [
                'reason' => 'recipient email is not configured',
            ]);

            $this->warn('ACTIVE_ADMINS_REPORT_EMAIL is not configured.');

            return 0;
        }

        ExportActiveAdminsJob::dispatch();

        $this->info('Active admins report export job dispatched.');

        return 0;
    }
}
