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
        ini_set('memory_limit','3072M');
        $user_id = $this->argument('user_id');

        $this->info("FIX USER {$user_id}");

        // get user transactions
        $user_transactions = DB::table('transactions')->where('user_id', $user_id)->select('id')->get()->pluck('id')->toArray();
        $user_transactions_count = count(array_unique($user_transactions));
        $this->info("User all transactions count = {$user_transactions_count}"); 
        
        // get user completed transfers
        $completed_transfers = DB::table('settlements')->where('user_id', $user_id)->where('status', 'completed')->select('id')->get()->pluck('id')->toArray();
        $completed_transfers_count = count($completed_transfers);
        $this->info("User completed transfers count = {$completed_transfers_count}");

        $completed_transfers_transactions = [];
        $uncompleted_transfers_transactions = [];

        if(!empty($completed_transfers)){
            // all completed transactions
            $completed_transfers_transactions = DB::table('transaction_transfer')->rightjoin('transactions', 'transaction_transfer.transaction_id', '=', 'transactions.id')->whereIn('transfer_id', $completed_transfers)->select('transaction_transfer.transaction_id AS trans_id')->get()->pluck('trans_id')->toArray();

            $completed_transfers_transactions_count = count($completed_transfers_transactions);
            $completed_transfers_transactions_unique_count = count(array_unique($completed_transfers_transactions));
            $this->info("COMPLETED TRANSFERS ALL TRANSACTIONS");
            $this->line("Completed transfers transactions count  = {$completed_transfers_transactions_count}");
            $this->line("Completed transfers transactions unique count = {$completed_transfers_transactions_unique_count}");

            // completed transactions need fix
            $uncompleted_transfers_transactions = DB::table('transaction_transfer')->rightjoin('transactions', 'transaction_transfer.transaction_id', '=', 'transactions.id')->whereIn('transfer_id', $completed_transfers)->where(function ($query) {$query->where('transactions.pending_settled', '!=', 1)->orWhere('transactions.settled', '!=', 1);})->select('transaction_transfer.transaction_id AS trans_id')->get()->pluck('trans_id')->toArray();

            $uncompleted_transfers_transactions_count = count($uncompleted_transfers_transactions);
            $uncompleted_transfers_transactions_unique_count = count(array_unique($uncompleted_transfers_transactions));
            $this->info("COMPLETED TRANSFERS TRANSACTIONS NEED FIX");
            $this->line("Completed transfers transactions count  = {$uncompleted_transfers_transactions_count}");
            $this->line("Completed transfers transactions unique count = {$uncompleted_transfers_transactions_unique_count}");
        }

        // get user pending transfers
        $pending_transfers = DB::table('settlements')->where('user_id', $user_id)->whereIn('status', ['pending', 'send_to_sps'])->select('id')->get()->pluck('id')->toArray();

        $pending_transfers_count = count($pending_transfers);
        $this->info("User pending and sps transfers count = {$pending_transfers_count}");

        $pending_transfers_transactions = [];
        $unpending_transfers_transactions = [];

        if(!empty($pending_transfers)){
            // all pending transactions
            $pending_transfers_transactions = DB::table('transaction_transfer')->rightjoin('transactions', 'transaction_transfer.transaction_id', '=', 'transactions.id')->whereIn('transfer_id', $pending_transfers)->select('transaction_transfer.transaction_id AS trans_id')->get()->pluck('trans_id')->toArray();

            $pending_transfers_transactions_count = count($pending_transfers_transactions);
            $pending_transfers_transactions_unique_count = count(array_unique($pending_transfers_transactions));
            $this->info("PENDING TRANSFERS ALL TRANSACTIONS");
            $this->line("Pending transfers transactions count = {$pending_transfers_transactions_count}");
            $this->line("Pending transfers transactions unique count = {$pending_transfers_transactions_unique_count}");

            // pending transactions need fix
            $unpending_transfers_transactions = DB::table('transaction_transfer')->rightjoin('transactions', 'transaction_transfer.transaction_id', '=', 'transactions.id')->whereIn('transfer_id', $pending_transfers)->where(function ($query) {$query->where('transactions.pending_settled', '!=', 1)->orWhere('transactions.settled', '!=', 0);})->select('transaction_transfer.transaction_id AS trans_id')->get()->pluck('trans_id')->toArray();

            $unpending_transfers_transactions_count = count($unpending_transfers_transactions);
            $unpending_transfers_transactions_unique_count = count(array_unique($unpending_transfers_transactions));
            $this->info("PENDING TRANSFERS TRANSACTIONS NEED FIX");
            $this->line("Pending transfers transactions count = {$unpending_transfers_transactions_count}");
            $this->line("Pending transfers transactions unique count = {$unpending_transfers_transactions_unique_count}");
        }

        // merge all transfers transactions
        $transfers_transactions_merged = array_merge($completed_transfers_transactions,$pending_transfers_transactions);
        $transfers_transactions_merged_count = count($transfers_transactions_merged);
        $transfers_transactions_merged_unique = count(array_unique($transfers_transactions_merged));
        $this->info("Merged transfers all transactions count = {$transfers_transactions_merged_count}");
        $this->info("Merged transfers all transactions unique count = {$transfers_transactions_merged_unique}");

        // merge transfers transctions need fix
        $fix_transfers_transactions_merged = array_merge($uncompleted_transfers_transactions,$unpending_transfers_transactions);
        $fix_transfers_transactions_merged_count = count($fix_transfers_transactions_merged);
        $fix_transfers_transactions_merged_unique = count(array_unique($fix_transfers_transactions_merged));
        $this->info("Merged transfers transactions need fix count = {$fix_transfers_transactions_merged_count}");
        $this->info("Merged transfers transactions need fix unique count = {$fix_transfers_transactions_merged_unique}");

        // get normal transactions need fix
        $merged_transfers = array_merge($completed_transfers,$pending_transfers);

        $transfers_transactions_merged_sub_query = DB::table('transaction_transfer')->rightjoin('transactions', 'transaction_transfer.transaction_id', '=', 'transactions.id')->whereIn('transfer_id', $merged_transfers)->select('transaction_transfer.transaction_id AS trans_id');

        $fix_normal_transactions = DB::table('transactions')->where('user_id', $user_id)->whereNotIn('id', $transfers_transactions_merged_sub_query)->where(function ($query) {$query->where('pending_settled', '!=', 0)->orWhere('settled', '!=', 0);})->select('id')->get()->pluck('id')->toArray();

        $fix_normal_transactions_count = count($fix_normal_transactions);
        $fix_normal_transactions_unique_count = count(array_unique($fix_normal_transactions));
        $this->info("NORMAL TRANSACTIONS NEED TO UNSETTLED");
        $this->line("Normal transactions count = {$fix_normal_transactions_count}");
        $this->line("Normal transactions unique count = {$fix_normal_transactions_unique_count}");

        if ($this->confirm('Do you wish to fix this user ?')) {
            $this->info('Fixing in proccessing');

            if(!empty($uncompleted_transfers_transactions)){
                // Settled completed transfer transactions
                $chunked_uncompleted_transactions = array_chunk($uncompleted_transfers_transactions, 100);
                foreach($chunked_uncompleted_transactions as $key => $uncompleted_trans){
                    Transaction::whereIn('id', $uncompleted_trans)->update(['pending_settled' => true, 'settled' => true]);
                    $this->line("Uncompleted transactions chunk {$key} finished");
                }
                
                $this->info("Settled completed transactions for user {$user_id} finished");
            }

            if(!empty($unpending_transfers_transactions)){
                // Pending settled pending and send to sps transfers transactions
                $chunked_unpending_transactions = array_chunk($unpending_transfers_transactions, 100);
                foreach($chunked_unpending_transactions as $key => $unpending_trans){
                    Transaction::whereIn('id', $unpending_trans)->update(['pending_settled' => true, 'settled' => false]);
                    $this->line("Unpending transactions chunk {$key} finished");
                }

                $this->info("Pending settled transactions for user {$user_id} finished");
            }

            if(!empty($fix_normal_transactions)){
                // Pending settled pending and send to sps transfers transactions
                $chunked_fix_normal_transactions = array_chunk($fix_normal_transactions, 100);
                foreach($chunked_fix_normal_transactions as $key => $fix_normal_trans){
                    Transaction::whereIn('id', $fix_normal_trans)->update(['pending_settled' => false, 'settled' => false]);
                    $this->line("Normal transactions Chunk {$key} finished");
                }

                $this->info("Unsettled normal transactions for user {$user_id} finished");
            }
    
            $this->info("Fixed user {$user_id} successfully");
        }else{
            $this->info('Fixing Request canceled');
        }


    }
}
