<?php

namespace App\Console\Commands;

use App\Services\OtpService;
use Illuminate\Console\Command;

class CleanupExpiredOtps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired OTP records from the database';

    /**
     * OTP Service instance.
     *
     * @var OtpService
     */
    protected $otpService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(OtpService $otpService)
    {
        parent::__construct();
        $this->otpService = $otpService;
    }

    /**
     * Execute the console command.
     *
     * This command deletes all expired OTP records from the database.
     * It should be scheduled to run daily to keep the database clean.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting cleanup of expired OTPs...');

        // Clean up expired OTPs
        $deleted = $this->otpService->cleanupExpired();

        $this->info("Successfully deleted {$deleted} expired OTP record(s).");

        return 0;
    }
}