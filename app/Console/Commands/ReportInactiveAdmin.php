<?php

namespace App\Console\Commands;

use App\Jobs\ExportInactiveAdmins;
use App\Models\Admin;
use Illuminate\Console\Command;

class ReportInactiveAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:report_inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for get admins who not login form specifiec time and report it to spacefic mail';

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
        $email = config('nova.send_to_mail');
        $days = config('nova.inactive_period_day');
        $date = date('Y-m-d', strtotime('-'.$days.' days'));
        ExportInactiveAdmins::dispatch($email, $date);
    }
}
