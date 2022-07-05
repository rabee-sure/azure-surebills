<?php

namespace App\Jobs;

use App\Models\Bill;
use App\Models\PaymentLog;
use App\Models\OfflineTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OfflineRefundTransactionsForOwner 
{
    use Dispatchable, SerializesModels;

    protected $bill;
    protected $total_remain;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Bill $bill, $total_remain)
    {
        $this->bill = $bill;
        $this->total_remain = $total_remain;
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
        $transaction->amount      = $this->total_remain;
        $transaction->reference   = $this->bill->number;
        $transaction->description = 'REFUND Bill ' . $this->bill->number . ' - ' . $this->bill->customer_name;
        $transaction->transaction_source = 'refund';
        $transaction->order = $order_max+1;
        $transaction->save();
    }
}
