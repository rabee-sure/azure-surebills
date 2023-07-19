<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;

class BlockPasswordForAdmins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:block_password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for block password of admin to force them to change it';

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
        $days = config('nova.change_password_period_day');
        $date = date('Y-m-d', strtotime('-'.$days.' days'));
        $admins = Admin::whereDate('last_change_password_at', '<=', $date)->update(['password_block' => true]);
    }
}
