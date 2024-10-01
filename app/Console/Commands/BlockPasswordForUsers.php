<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class BlockPasswordForUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merchants:block_password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for block password of merchants to force them to change it';

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
        $days = config('auth.change_password_period_day');
        $date = date('Y-m-d', strtotime('-'.$days.' days'));
        $users = User::whereDate('last_change_password_at', '<=', $date)->update(['password_block' => true]);
    }
}
