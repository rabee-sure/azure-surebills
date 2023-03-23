<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CalculateTransferAmount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:calculate {user_id} {period_start} {period_end}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for calculate transfer amount';

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
        $this->info('- Get sum of all transfers transactions');
        $sum_transfers_transactions = Transaction::where('user_id', $this->argument('user_id'))->where('transaction_source', '=', "transfer")->sum('amount');
        $this->line('Total transfers amount = '.$sum_transfers_transactions);

        $this->info('- Get sum of all transactions witout transfers');
        $transcactions_without_transfers_obj = Transaction::where('user_id', $this->argument('user_id'))->where('transaction_source', '!=', "transfer")->select(DB::raw("SUM(CASE WHEN type  = 'credit' THEN amount ELSE 0 END) AS credit_total,SUM(CASE WHEN type  = 'debit' THEN amount ELSE 0 END) AS debit_total"))->first();
        $this->line('Total credit = '.$transcactions_without_transfers_obj->credit_total);
        $this->line('Total debit = '.$transcactions_without_transfers_obj->debit_total);
        $balance_amount_without_transfer = $transcactions_without_transfers_obj->credit_total - $transcactions_without_transfers_obj->debit_total;
        $this->line('Balance without transfer = '.$balance_amount_without_transfer);
        $current_balance = $balance_amount_without_transfer - $sum_transfers_transactions;
        $this->line('Current balance = '.$current_balance);

        $this->info('- Calculate balance from query');
        $balance_obj = Transaction::where('user_id', $this->argument('user_id'))->select(DB::raw("SUM(CASE WHEN type  = 'credit' THEN amount ELSE 0 END) AS credit_total,SUM(CASE WHEN type  = 'debit' THEN amount ELSE 0 END) AS debit_total"))->first();
        $this->line('Total credit = '.$balance_obj->credit_total);
        $this->line('Total debit = '.$balance_obj->debit_total);
        $current_balance_from_query = $balance_obj->credit_total - $balance_obj->debit_total;
        $this->line('Current balance from query = '.$current_balance_from_query);

        if((Int)$current_balance == (Int)$current_balance_from_query){
            $this->info('---- Balance caclulation True ----');
        }else{
            $this->info('---- Balance caclulation Wrong ----');
        }

        $this->info('- Calculate transfer amount');
        $after_period_balance_obj = Transaction::where('user_id', $this->argument('user_id'))->where('transaction_source', '!=', "transfer")->whereDate('created_at', '>', $this->argument('period_start'))->select(DB::raw("SUM(CASE WHEN type  = 'credit' THEN amount ELSE 0 END) AS credit_total,SUM(CASE WHEN type  = 'debit' THEN amount ELSE 0 END) AS debit_total"))->first();
        $this->line('Total credit after period = '.$after_period_balance_obj->credit_total);
        $this->line('Total debit after period = '.$after_period_balance_obj->debit_total);
        $after_period_balance = $after_period_balance_obj->credit_total - $after_period_balance_obj->debit_total;
        $this->line('Balance after period = '.$current_balance_from_query);

        $new_transfer_amount = $current_balance - $after_period_balance;
        $this->line('New transfer amount = '.$new_transfer_amount);

        $this->info('- Get period balance will be transfered');
        $period_balance_obj = Transaction::where('user_id', $this->argument('user_id'))->whereDate('created_at', '>', $this->argument('period_start'))->whereDate('created_at', '<=', $this->argument('period_end'))->select(DB::raw("SUM(CASE WHEN type  = 'credit' THEN amount ELSE 0 END) AS credit_total,SUM(CASE WHEN type  = 'debit' THEN amount ELSE 0 END) AS debit_total"))->first();
        $this->line('Total period credit = '.$period_balance_obj->credit_total);
        $this->line('Total period debit = '.$period_balance_obj->debit_total);
        $period_balance = $period_balance_obj->credit_total - $period_balance_obj->debit_total;
        $this->line('Period Balance = '.$period_balance);

        if((Int)$new_transfer_amount == (Int)$period_balance){
            $this->info('---- Transfer amount caclulation True ----');
        }else{
            $this->info('---- Transfer amount caclulation Wrong ----');
        }

    }
}