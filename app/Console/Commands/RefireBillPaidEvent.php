<?php

namespace App\Console\Commands;

use App\Events\BillPaid;
use App\Models\Bill;
use App\Models\PaymentLog;
use Illuminate\Console\Command;

class RefireBillPaidEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:refire_bill_paid_event {bill_id} {payment_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command for refire BillPaid event for specfiec bill you must sent first argument bill_id and seconde one payment_id of bill paid paymentLog';

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
        $bill = Bill::find($this->argument('bill_id'));
        $payment = PaymentLog::find($this->argument('payment_id'));

        // dd($bill, $payment);
        event(new BillPaid($bill, $payment));
    }
}
