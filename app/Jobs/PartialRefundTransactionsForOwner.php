<?php

namespace App\Jobs;

use App\Models\Bill;
use App\Models\PaymentLog;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PartialRefundTransactionsForOwner 
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
        $percentage = $this->bill->pricing['fees_percentage'];
        $fixed = $this->bill->pricing['fees_fixed'];

        $payment_fees = $this->amount * ($percentage / 100);
        $payment_fees_vat = $payment_fees * ($this->bill->pricing['vat_percentage'] / 100);

        $order_max = Transaction::where('bill_id', $this->bill->id)->max('order');


        $transaction = new Transaction;
        $transaction->user_id     = $this->bill->user_id;
        $transaction->bill_id     = $this->bill->id;
        $transaction->type        = 'debit';
        $transaction->amount      = $this->amount;
        $transaction->reference   = $this->bill->number;
        $transaction->description = 'PARTIAL REFUND Bill ' . $this->bill->number . ' - ' . $this->bill->customer_name;
        $transaction->transaction_source = 'refund';
        $transaction->order = $order_max+1;
        $transaction->save();
        
        if($this->bill->user->able_refund_with_fees){
            //withdrawBillFees
            $transaction = new Transaction;
            $transaction->user_id     = $this->bill->user_id;
            $transaction->bill_id     = $this->bill->id;
            $transaction->type        = 'credit';
            $transaction->amount      = $payment_fees;
            $transaction->reference   = $this->bill->number;
            $transaction->description = 'PARTIAL REFUND Fee';
            $transaction->transaction_source = 'refund';
            $transaction->order = $order_max+2;
            $transaction->save();

            //withdrawBillVat
            $transaction = new Transaction;
            $transaction->user_id     = $this->bill->user_id;
            $transaction->bill_id     = $this->bill->id;
            $transaction->type        = 'credit';
            $transaction->amount      = $payment_fees_vat;
            $transaction->reference   = $this->bill->number;
            $transaction->description = 'PARTIAL REFUND VAT';
            $transaction->transaction_source = 'refund';
            $transaction->order = $order_max+2;
            $transaction->save();
        }
    }
}
