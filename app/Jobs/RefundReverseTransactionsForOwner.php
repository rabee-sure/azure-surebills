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

class RefundReverseTransactionsForOwner 
{
    use Dispatchable, SerializesModels;

    protected $bill;

    protected $log;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Bill $bill, PaymentLog $payment_log)
    {
        $this->bill = $bill;
        $this->log = $payment_log;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $logResponse = isset($this->log->results['response']) ? $this->log->results['response'] : [];
        $order_max = Transaction::where('bill_id', $this->bill->id)->max('order');

        $dash = $this->bill->customer_name ? '-' : '';

        $transaction = new Transaction;
        $transaction->user_id     = $this->bill->user_id;
        $transaction->bill_id     = $this->bill->id;
        $transaction->type        = 'credit';
        $transaction->amount      = $this->bill->total;
        $transaction->reference   = $this->bill->number;
        $transaction->description = 'REFUND Bill ' . $this->bill->number .' '.$dash.' '. $this->bill->customer_name.' - Reversed Transaction For Failed Refund';
        $transaction->auth_id     = $this->log->bank_transaction_id;
        $transaction->card_brand  = $this->log->brand;
        $transaction->card        = $this->log->card_number;
        $transaction->transaction_source = 'refund';
        $transaction->order = $order_max+1;
        $transaction->save();

        if($this->bill->user->able_refund_with_fees){
            //withdrawBillFees
            $transaction = new Transaction;
            $transaction->user_id     = $this->bill->user_id;
            $transaction->bill_id     = $this->bill->id;
            $transaction->type        = 'debit';
            $transaction->amount      = $this->bill->payment_fees;
            $transaction->reference   = $this->bill->number;
            $transaction->description = 'REFUND Fee - Reversed Transaction For Failed Refund';
            $transaction->transaction_source = 'refund';
            $transaction->order = $order_max+2;
            $transaction->save();

            //withdrawBillVat
            $transaction = new Transaction;
            $transaction->user_id     = $this->bill->user_id;
            $transaction->bill_id     = $this->bill->id;
            $transaction->type        = 'debit';
            $transaction->amount      = $this->bill->payment_fees_vat;
            $transaction->reference   = $this->bill->number;
            $transaction->description = 'REFUND VAT - Reversed Transaction For Failed Refund';
            $transaction->transaction_source = 'refund';
            $transaction->order = $order_max+3;
            $transaction->save();
        }

        $this->log->webhook_response_received = true;
        $this->log->save();
    }
}
