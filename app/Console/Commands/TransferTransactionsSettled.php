<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\Transfer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TransferTransactionsSettled extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:transaction_settled {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for re settled all user transaction as per as his transfers';

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
        $user_id = $this->argument('user_id');

        $this->info("start with user {$user_id}");

        $transaction = DB::transaction(function () use ($user_id) {
            // get user completed transfers
            $completed_transfers = DB::table('settlements')->where('user_id', $user_id)->where('status', 'completed')->select('id')->get()->pluck('id')->toArray();
    
            if(!empty($completed_transfers)){
                $completed_transfers_transactions = DB::table('transaction_transfer')->whereIn('transfer_id', $completed_transfers)->select('transaction_id')->get()->pluck('transaction_id')->toArray();
                $completed_transfers_transactions_count = count($completed_transfers_transactions);
                $this->info("Completed Transactions = {$completed_transfers_transactions_count}");
            }
    
            // get user pending transfers
            $pending_transfers = DB::table('settlements')->where('user_id', $user_id)->where('status', 'pending')->orWhere('status', 'send_to_sps')->select('id')->get()->pluck('id')->toArray();
    
            if(!empty($pending_transfers)){
                $pending_transfers_transactions = DB::table('transaction_transfer')->whereIn('transfer_id', $pending_transfers)->select('transaction_id')->get()->pluck('transaction_id')->toArray();
                $pending_transfers_transactions_count = count($pending_transfers_transactions);
                $this->info("Pending Transactions = {$pending_transfers_transactions_count}");
            }
    
            // Unsettled all user transactions
            Transaction::where('user_id', $user_id)->update(['pending_settled' => false, 'settled' => false]);
            $this->info("Unsettled all transactions for user {$user_id}");
    
            if(!empty($completed_transfers_transactions)){
                // Settled completed transfer transactions
                Transaction::whereIn('id', $completed_transfers_transactions)->update(['pending_settled' => true, 'settled' => true]);
                $this->info("Settled completed transactions for user {$user_id}");
            }
    
            if(!empty($pending_transfers_transactions)){
                // Pending settled pending and send to sps transfers transactions
                Transaction::whereIn('id', $pending_transfers_transactions)->update(['pending_settled' => true, 'settled' => false]);
                $this->info("Pending Settled pending transactions for user {$user_id}");
            }
    
            $this->info("Finish user {$user_id}");
        });
    }
}
