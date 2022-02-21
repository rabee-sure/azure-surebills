<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveDuplicatedTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:duplicatedtransactions {source  : transaction source}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove duplicated transactions from db';

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
        $transactions = Transaction::where('settled', 0)
            ->where('pending_settled', 0)
            ->where('transaction_source', $this->argument('source'))
            ->select(
                DB::raw('COUNT(bill_id) as n'),
                'bill_id'
            )
            ->having('n', '>', 1)
            ->groupBy('bill_id')
            ->get();

        foreach ($transactions as $transaction) {
            $transactionToDelete = Transaction::where('bill_id', $transaction->bill_id)
                ->where('settled', 0)
                ->where('pending_settled', 0)
                ->where('transaction_source', $this->argument('source'))
                ->orderBy('created_at', 'asc')
                ->first();

            $transactionToDelete->delete();
            var_dump("Done: " . $transactionToDelete->id);
        }

        return true;
    }
}
