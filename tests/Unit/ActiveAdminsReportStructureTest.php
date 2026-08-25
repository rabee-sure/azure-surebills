<?php

namespace Tests\Unit;

use App\Exports\ActiveAdminsExport;
use PHPUnit\Framework\TestCase;

class ActiveAdminsReportStructureTest extends TestCase
{
    public function test_export_headings_exclude_sensitive_fields()
    {
        $headings = array_map('strtolower', (new ActiveAdminsExport())->headings());

        $this->assertContains('id', $headings);
        $this->assertContains('name', $headings);
        $this->assertContains('email', $headings);
        $this->assertContains('roles', $headings);
        $this->assertNotContains('password', $headings);
        $this->assertNotContains('remember_token', $headings);
        $this->assertNotContains('remember token', $headings);
    }

    public function test_command_only_dispatches_the_export_job()
    {
        $source = file_get_contents($this->projectPath('app/Console/Commands/ReportActiveAdmins.php'));

        $this->assertStringContainsString('ExportActiveAdminsJob::dispatch()', $source);
        $this->assertStringNotContainsString('Excel::', $source);
        $this->assertStringNotContainsString('Mail::', $source);
        $this->assertStringNotContainsString('Admin::', $source);
        $this->assertStringNotContainsString('Storage::', $source);
    }

    public function test_scheduler_reads_cron_expression_from_config()
    {
        $source = file_get_contents($this->projectPath('app/Console/Kernel.php'));

        $this->assertStringContainsString("command('admin:report_active')", $source);
        $this->assertStringContainsString("config('active_admins_report.schedule')", $source);
        $this->assertStringContainsString('->cron($activeAdminsReportSchedule)', $source);
        $this->assertStringNotContainsString("command('admin:report_active')->dailyAt", $source);
    }

    public function test_export_job_dispatches_email_job_after_store()
    {
        $source = file_get_contents($this->projectPath('app/Jobs/ExportActiveAdminsJob.php'));

        $this->assertStringContainsString('ActiveAdminsExport', $source);
        $this->assertStringContainsString('->store($filePath, $disk)', $source);
        $this->assertStringContainsString('SendActiveAdminsReportJob::dispatch($filePath)', $source);
        $this->assertStringNotContainsString('Mail::', $source);
    }

    public function test_email_job_sends_then_deletes_only_its_file()
    {
        $source = file_get_contents($this->projectPath('app/Jobs/SendActiveAdminsReportJob.php'));

        $this->assertStringContainsString("config('active_admins_report.email')", $source);
        $this->assertStringContainsString('ActiveAdminsReportMail', $source);
        $this->assertStringContainsString('Storage::disk($disk)->delete($this->filePath)', $source);
        $this->assertStringContainsString('deleteGeneratedFile', $source);
    }

    public function test_env_example_documents_required_variables()
    {
        $source = file_get_contents($this->projectPath('.env.example'));

        $this->assertStringContainsString('ACTIVE_ADMINS_REPORT_EMAIL=', $source);
        $this->assertStringContainsString('ACTIVE_ADMINS_REPORT_SCHEDULE=', $source);
    }

    /**
     * @param  string  $relative
     * @return string
     */
    protected function projectPath($relative)
    {
        return dirname(__DIR__, 2).'/'.$relative;
    }
}
