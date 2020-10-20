<?php

namespace App\Console\Commands;

use App\Bill;
use App\Transaction;
use Illuminate\Console\Command;

class AddOldTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:oldtransactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add old bills transactions';

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
        foreach (Bill::paid()->doesntHave('depositTransaction')->get() as $bill) {
            $payment = $bill->success_payment;
            if ($payment) {
                Transaction::deposit('bill', $payment);
                var_dump('a');
            }
        }

        return true;
    }
}
