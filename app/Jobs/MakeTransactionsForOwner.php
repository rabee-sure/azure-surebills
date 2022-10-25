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

class MakeTransactionsForOwner
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
        $dn = $this->bill->debit_note_bill_id ? 'DN' : '';

        $transaction = new Transaction;
        $transaction->user_id     = $this->bill->user_id;
        $transaction->bill_id     = $this->bill->id;
        $transaction->type        = 'credit';
        $transaction->amount      = ($this->bill->total - $this->bill->channel_extra_amount - $this->bill->channel_extra_vat);
        $transaction->reference   = $this->bill->number;
        $transaction->description = 'Bill ' . $dn.''.$this->bill->number . ' - ' . $this->bill->customer_name;
        $transaction->auth_id     = $this->log->bank_transaction_id;
        if ($this->log->payment_method == 'mastercard_applepay') {
            $transaction->card_brand  = 'APPLEPAY - ' . $this->log->brand;
            $transaction->card        = $this->log->card_number;
        } else {
            $transaction->card_brand  = $this->log->brand;
            $transaction->card        = $this->log->card_number;
        }
        $transaction->transaction_source = 'bill';
        $transaction->saveIfUnique();

        //withdrawBillFees
        $transaction = new Transaction;
        $transaction->user_id     = $this->bill->user_id;
        $transaction->bill_id     = $this->bill->id;
        $transaction->type        = 'debit';
        $transaction->amount      = $this->bill->payment_fees;
        $transaction->reference   = $this->bill->number;
        $transaction->description = 'Fee - Transaction Processing';
        $transaction->transaction_source = 'fees';
        $transaction->saveIfUnique();

        //withdrawBillVat
        $transaction = new Transaction;
        $transaction->user_id     = $this->bill->user_id;
        $transaction->bill_id     = $this->bill->id;
        $transaction->type        = 'debit';
        $transaction->amount      = $this->bill->payment_fees_vat;
        $transaction->reference   = $this->bill->number;
        $transaction->description = 'VAT - Transaction Processing';
        $transaction->transaction_source = 'vat';
        $transaction->saveIfUnique();
    }
}
