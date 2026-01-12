<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email : The email address to send the test to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify mail configuration';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->argument('email');
 
        try {
            Mail::raw('This is a test email from Laravel. Your mail configuration is working fine 🚀', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Laravel Test Email');
            });
 
            $this->info("✅ Test email sent successfully to {$email}");
        } catch (\Throwable $e) {
            $this->error("❌ Failed to send email:");
            $this->error($e->getMessage());
        }
    }
}
