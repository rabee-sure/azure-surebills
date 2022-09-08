<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;

class UpdateTransactionsBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:balance {user_id} {transaction_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'run query to update transaction balance after delete duplication';

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
        $transaction_id = $this->argument('transaction_id');

        $duplicatedTransaction = Transaction::findOrFail($transaction_id);

        $duplicated_at = date('Y-m-d H:i:s', strtotime($duplicatedTransaction->created_at));
        $amount = $duplicatedTransaction->amount;

        $transactions_after_duplication = Transaction::where('created_at', '>', $duplicated_at)->where('user_id', $user_id)->get();

        \Log::channel('duplicated_transactions')->info("deleted transaction", array($duplicatedTransaction));
        $duplicatedTransaction->delete();

        foreach($transactions_after_duplication as $transaction){
            \Log::channel('duplicated_transactions')->info("updated transaction", array($transaction));
            $transaction->balance += $amount;
            $transaction->save();
        }
    }
}