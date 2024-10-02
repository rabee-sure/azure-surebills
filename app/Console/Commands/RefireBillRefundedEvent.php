<?php

namespace App\Console\Commands;

use App\Events\BillPaid;
use App\Models\Bill;
use App\Models\PaymentLog;
use Illuminate\Console\Command;

class RefireBillRefundedEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:refire_bill_refunded_event {bill_id} {payment_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command for refire BillRefunded event for specfiec bill you must sent first argument bill_id and seconde one payment_id of bill paid paymentLog';

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

        $this->info('Bill total : '.$bill->total);
        $this->info('Mastercard transaction amount : '.$payment->results['transaction']['amount']);
        if($this->confirm('Do you to refire refund event?')){
            if ($bill->total == $payment->results['transaction']['amount']) {
                $this->info('Total refund');
                $bill->fireRefundEvent($payment);
                $this->info('Refund event fired');
            } else {
                $this->info('Partial refund');
                $bill->fireRefundEvent($payment, $payment->results['transaction']['amount']);
                $this->info('Refund event fired');
            }
        }
    }
}
