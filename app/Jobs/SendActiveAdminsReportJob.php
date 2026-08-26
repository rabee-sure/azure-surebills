<?php

namespace App\Jobs;

use App\Mail\ActiveAdminsReportMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendActiveAdminsReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Relative path on the configured disk for this execution only.
     *
     * @var string
     */
    public $filePath;

    /**
     * @param  string  $filePath
     * @return void
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @return void
     */
    public function handle()
    {
        Log::info('Active admins report email sending started', [
            'file' => $this->filePath,
        ]);

        $disk = config('active_admins_report.disk');
        $email = config('active_admins_report.email');

        if (!$email) {
            Log::error('Active admins report failed', [
                'stage' => 'email',
                'reason' => 'recipient email is not configured',
            ]);

            throw new \RuntimeException('Active admins report recipient is not configured.');
        }

        if (!Storage::disk($disk)->exists($this->filePath)) {
            Log::error('Active admins report failed', [
                'stage' => 'email',
                'reason' => 'generated file is missing',
                'file' => $this->filePath,
            ]);

            throw new \RuntimeException('Active admins report file was not found.');
        }

        Mail::to($email)->send(new ActiveAdminsReportMail($this->filePath));

        Log::info('Active admins report email sent', [
            'file' => $this->filePath,
        ]);

        $this->deleteGeneratedFile($disk);
    }

    /**
     * @param  string  $disk
     * @return void
     */
    protected function deleteGeneratedFile($disk)
    {
        try {
            Storage::disk($disk)->delete($this->filePath);

            Log::info('Active admins report file deleted', [
                'file' => $this->filePath,
            ]);
        } catch (\Throwable $exception) {
            Log::error('Active admins report failed', [
                'stage' => 'cleanup',
                'file' => $this->filePath,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error('Active admins report failed', [
            'stage' => 'email',
            'file' => $this->filePath,
            'message' => $exception->getMessage(),
        ]);
    }
}
