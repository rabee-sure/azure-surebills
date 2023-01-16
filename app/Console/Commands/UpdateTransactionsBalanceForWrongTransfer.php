<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;

class UpdateTransactionsBalanceForWrongTransfer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer_transactions:balance {user_id} {date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'run query to update transaction balance after last transfer';

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
        $date = $this->argument('date');

        $transactions_after_last_trnasfer = Transaction::where('created_at', '>', $date)->where('user_id', $user_id)->orderBy('created_at', 'asc')->get();

        \Log::channel('duplicated_transactions')->info("changed Transactions", array($transactions_after_last_trnasfer));

        $previous_balance = 0;

        foreach($transactions_after_last_trnasfer as $transaction){
            \Log::channel('duplicated_transactions')->info("updated transaction", array($transaction));
            if($transaction->type == 'credit'){
                $transaction->balance = $previous_balance + $transaction->amount;
            }

            if($transaction->type == 'debit'){
                $transaction->balance = $previous_balance - $transaction->amount;
            }

            $transaction->save();
            
            $previous_balance = $transaction->balance;
        }
    }
}