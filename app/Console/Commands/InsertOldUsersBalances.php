<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\UserBalance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InsertOldUsersBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:insert_old_balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for calculate old users balances and insert it to user_balances table';

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
        $start = microtime(true);
        $usersBalances = Transaction::groupBy('user_id')
            ->select(DB::raw("user_id ,(SUM(CASE WHEN type  = 'credit' THEN amount ELSE 0 END)) - (SUM(CASE WHEN type  = 'debit' THEN amount ELSE 0 END)) AS balance"))->get()->toArray();

            
        // chunk users balances
        UserBalance::chunk(100, function($userBalances) use ($usersBalances){
            foreach($userBalances as $userBalance){
                // create or update user balance
                UserBalance::updateOrCreate(
                    ['user_id' => $userBalance['user_id']],
                    ['balance' => $userBalance['balance']]
                );
            }
        });

        $time_elapsed_secs = microtime(true) - $start;
        $this->info('time spent '.$time_elapsed_secs);
    }
}
