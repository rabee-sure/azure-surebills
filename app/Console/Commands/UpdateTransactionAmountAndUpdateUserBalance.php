<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;

class UpdateTransactionAmountAndUpdateUserBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tranasction:update {transaction_id} {user_id} {new_amount}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for update transaction amount and recalculate balance after this transaction';

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
        $new_amount = $this->argument('new_amount');

        $target_transaction = Transaction::findOrFail($transaction_id);
        $created_at = date('Y-m-d H:i:s', strtotime($target_transaction->created_at));
        $old_amount = $target_transaction->amount;
        
        $transactions_after_target_transaction = Transaction::where('created_at', '>', $created_at)->where('user_id', $user_id)->get();
        
        $diff = $new_amount - $old_amount;
        $target_transaction->amount = $new_amount;
        
        if($target_transaction->type == 'credit'){
            $target_transaction->balance += $diff;
            $target_transaction->save();

            foreach($transactions_after_target_transaction as $transaction){
                $transaction->balance += $diff;
                $transaction->save();
            }
        }

        if($target_transaction->type == 'debit'){
            $target_transaction->balance -= $diff;
            $target_transaction->save();

            foreach($transactions_after_target_transaction as $transaction){
                $transaction->balance -= $diff;
                $transaction->save();
            }
        }
    }
}
