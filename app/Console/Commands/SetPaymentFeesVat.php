<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\Transaction;
use Illuminate\Console\Command;

class SetPaymentFeesVat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:updatevat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update field payment_fees_vat on old Bills';

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
        foreach (Bill::paid()->whereNull('payment_fees_vat')->get() as $bill) {
            $bill->payment_fees_vat = $bill->payment_fees * (Transaction::VAT_PERCENTAGE / 100);
            $bill->save();
        }

        return true;
    }
}
