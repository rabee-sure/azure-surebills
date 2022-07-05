<?php

namespace App\Jobs;

use App\Models\Bill;
use App\Models\OfflineTransaction;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MakeOfflineTransactionsForOwner
{
    use Dispatchable, SerializesModels;

    protected $bill;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Bill $bill)
    {
        $this->bill = $bill;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $transaction = new OfflineTransaction;
        $transaction->user_id     = $this->bill->user_id;
        $transaction->bill_id     = $this->bill->id;
        $transaction->type        = 'credit';
        $transaction->amount      = $this->bill->total;
        $transaction->reference   = $this->bill->number;
        $transaction->description = 'Bill ' . $this->bill->number . ' - ' . $this->bill->customer_name;
        $transaction->transaction_source = 'bill';
        $transaction->saveIfUnique();
    }
}
