<?php

namespace Tests\Feature;

use App\Exports\ActiveAdminsExport;
use App\Jobs\ExportActiveAdminsJob;
use App\Jobs\SendActiveAdminsReportJob;
use App\Mail\ActiveAdminsReportMail;
use App\Models\Admin;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mockery;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class ActiveAdminsReportTest extends TestCase
{
    /**
     * @var array
     */
    protected $createdAdminIds = [];

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'active_admins_report.email' => 'active-admins-report@example.com',
            'active_admins_report.schedule' => '15 9 * * 1',
            'active_admins_report.disk' => 'local',
            'active_admins_report.directory' => 'reports/active_admins',
        ]);
    }

    protected function tearDown(): void
    {
        if (!empty($this->createdAdminIds)) {
            Admin::withTrashed()->whereIn('id', $this->createdAdminIds)->forceDelete();
            $this->createdAdminIds = [];
        }

        parent::tearDown();
    }

    public function test_command_dispatches_the_export_job()
    {
        Queue::fake();

        $this->artisan('admin:report_active')
            ->expectsOutput('Active admins report export job dispatched.')
            ->assertExitCode(0);

        Queue::assertPushed(ExportActiveAdminsJob::class);
        Queue::assertNotPushed(SendActiveAdminsReportJob::class);
    }

    public function test_scheduler_uses_the_configured_cron_expression()
    {
        $schedule = new Schedule();
        $kernel = $this->app->make(\App\Console\Kernel::class);

        $method = (new \ReflectionClass($kernel))->getMethod('schedule');
        $method->setAccessible(true);
        $method->invoke($kernel, $schedule);

        $event = collect($schedule->events())->first(function ($scheduledEvent) {
            return is_string($scheduledEvent->command)
                && strpos($scheduledEvent->command, 'admin:report_active') !== false;
        });

        $this->assertNotNull($event);
        $this->assertSame('15 9 * * 1', $event->expression);
    }

    public function test_export_query_includes_only_active_admins()
    {
        $active = $this->createTestAdmin(['is_active' => true]);
        $inactive = $this->createTestAdmin(['is_active' => false]);

        $ids = (new ActiveAdminsExport())->query()->pluck('id');

        $this->assertTrue($ids->contains($active->id));
        $this->assertFalse($ids->contains($inactive->id));
    }

    public function test_export_excludes_sensitive_fields_and_writes_xlsx()
    {
        Storage::fake('local');
        Queue::fake();

        $password = 'SuperSecretPass123!';
        $rememberToken = 'remember-token-'.Str::random(20);

        $active = $this->createTestAdmin([
            'is_active' => true,
            'password' => bcrypt($password),
            'remember_token' => $rememberToken,
        ]);
        $this->createTestAdmin(['is_active' => false]);

        (new ExportActiveAdminsJob())->handle();

        Queue::assertPushed(SendActiveAdminsReportJob::class, function ($job) {
            return is_string($job->filePath) && substr($job->filePath, -5) === '.xlsx';
        });

        $files = Storage::disk('local')->allFiles('reports/active_admins');
        $this->assertCount(1, $files);

        $absolutePath = Storage::disk('local')->path($files[0]);
        $rows = IOFactory::load($absolutePath)->getActiveSheet()->toArray(null, true, true, true);
        $headings = array_map('strtolower', array_values($rows[1]));
        $contents = strtolower(json_encode($rows));

        $this->assertNotContains('password', $headings);
        $this->assertNotContains('remember_token', $headings);
        $this->assertNotContains('remember token', $headings);
        $this->assertStringNotContainsString(strtolower($password), $contents);
        $this->assertStringNotContainsString(strtolower($rememberToken), $contents);
        $this->assertStringNotContainsString(strtolower($active->password), $contents);

        $exportedIds = [];
        foreach ($rows as $index => $row) {
            if ($index === 1) {
                continue;
            }
            $exportedIds[] = (int) $row['A'];
        }

        $this->assertContains($active->id, $exportedIds);
    }

    public function test_email_job_uses_configured_recipient_and_attaches_file()
    {
        Storage::fake('local');
        Mail::fake();

        $filePath = 'reports/active_admins/active_admins_report_test.xlsx';
        Storage::disk('local')->put($filePath, 'xlsx-bytes');

        (new SendActiveAdminsReportJob($filePath))->handle();

        Mail::assertSent(ActiveAdminsReportMail::class, function ($mail) use ($filePath) {
            return $mail->hasTo('active-admins-report@example.com')
                && $mail->filePath === $filePath
                && $mail->hasSubject('Active Admins Report');
        });
    }

    public function test_successful_email_deletes_the_generated_file()
    {
        Storage::fake('local');
        Mail::fake();

        $filePath = 'reports/active_admins/active_admins_report_delete.xlsx';
        Storage::disk('local')->put($filePath, 'xlsx-bytes');

        (new SendActiveAdminsReportJob($filePath))->handle();

        Storage::disk('local')->assertMissing($filePath);
    }

    public function test_failed_email_keeps_the_generated_file()
    {
        Storage::fake('local');

        $filePath = 'reports/active_admins/active_admins_report_keep.xlsx';
        Storage::disk('local')->put($filePath, 'xlsx-bytes');

        $pendingMail = Mockery::mock();
        $pendingMail->shouldReceive('send')->once()->andThrow(new \RuntimeException('smtp failed'));
        Mail::shouldReceive('to')->once()->with('active-admins-report@example.com')->andReturn($pendingMail);

        try {
            (new SendActiveAdminsReportJob($filePath))->handle();
            $this->fail('Expected the email job to throw when sending fails.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('smtp failed', $exception->getMessage());
        }

        Storage::disk('local')->assertExists($filePath);
    }

    /**
     * Insert a disposable admin row without firing Admin boot mail/jobs.
     *
     * @param  array  $attributes
     * @return \App\Models\Admin
     */
    protected function createTestAdmin(array $attributes = [])
    {
        $suffix = str_replace('.', '', uniqid('', true));

        $admin = null;

        Admin::withoutEvents(function () use (&$admin, $attributes, $suffix) {
            Admin::unguarded(function () use (&$admin, $attributes, $suffix) {
                $admin = Admin::create(array_merge([
                    'name' => 'Active Admins Report Test '.$suffix,
                    'email' => 'active-admins-report-'.$suffix.'@example.test',
                    'mobile' => substr(preg_replace('/\D/', '', $suffix).'500000000', 0, 9),
                    'password' => bcrypt(Str::random(16)),
                    'is_active' => true,
                    'password_block' => false,
                ], $attributes));
            });
        });

        $this->createdAdminIds[] = $admin->id;

        return $admin;
    }
}
