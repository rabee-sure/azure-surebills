<?php

namespace App\Jobs;

use App\Models\Bill;
use App\Models\OfflineTransaction;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OfflinePartialRefundTransactionsForOwner 
{
    use Dispatchable, SerializesModels;

    protected $bill;

    protected $amount;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Bill $bill, $amount)
    {
        $this->bill = $bill;
        $this->amount = $amount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order_max = OfflineTransaction::where('bill_id', $this->bill->id)->max('order');


        $transaction = new OfflineTransaction;
        $transaction->user_id     = $this->bill->user_id;
        $transaction->bill_id     = $this->bill->id;
        $transaction->type        = 'debit';
        $transaction->amount      = $this->amount;
        $transaction->reference   = $this->bill->number;
        $transaction->description = 'PARTIAL REFUND Bill ' . $this->bill->number . ' - ' . $this->bill->customer_name;
        $transaction->transaction_source = 'refund';
        $transaction->order = $order_max+1;
        $transaction->save();
    }
}
