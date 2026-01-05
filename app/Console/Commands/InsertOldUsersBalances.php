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

        // جلب وحساب الأرصدة
        $usersBalances = Transaction::groupBy('user_id')
            ->select(
                'user_id',
                DB::raw("SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) - SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END) AS balance")
            )->get()->toArray();

        $this->info('Total users balances to process: ' . count($usersBalances));

        if (empty($usersBalances)) {
            $this->info('No user balances to insert.');
            return 0;
        }

        $this->info('Start inserting users balances...');

        // تقسيم المصفوفة إلى دفعات حجم كل منها 100
        $userBalanceChunks = array_chunk($usersBalances, 100);

        foreach ($userBalanceChunks as $chunk) {
            // استخدام upsert لإجراء عمليات create/update بكفاءة
            UserBalance::upsert($chunk, ['user_id'], ['balance']);
        }

        $this->info(count($usersBalances) . ' user balances have been inserted/updated.');

        $time_elapsed_secs = microtime(true) - $start;
        $this->info('Time spent: ' . $time_elapsed_secs . ' seconds');

        return 0;
    }

}
