<?php

namespace App\Jobs;

use App\Bill;
use App\PaymentLog;
use App\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MakeTransactionsForOwner implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $authorizeId = isset($logResponse['resultDetails']) && isset($logResponse['resultDetails']['AuthorizeId']) ? $logResponse['resultDetails']['AuthorizeId'] : null;

        $transaction = new Transaction;
        $transaction->user_id     = $this->bill->user_id;
        $transaction->bill_id     = $this->bill->id;
        $transaction->type        = 'credit';
        $transaction->amount      = isset($logResponse['amount']) ? $logResponse['amount'] : 0;
        $transaction->reference   = $this->bill->number;
        $transaction->description = 'Bill ' . $this->bill->number . ' - ' . $this->bill->customer_name;
        $transaction->auth_id     = $authorizeId;
        if (isset($logResponse['paymentBrand']) && $this->log->payment_method != 'hyperpay_applepay') {
            $transaction->card_brand  = $logResponse['paymentBrand'];
            $transaction->card        = 'XXX' . $logResponse['card']['last4Digits'];
        } else if (isset($logResponse['card']) && $this->log->payment_method == 'hyperpay_applepay') {
            $transaction->card_brand  = 'APPLEPAY';
            $transaction->card        = 'XXX' . $logResponse['card']['last4Digits'];
        }
        $transaction->transaction_source = 'bill';
        $transaction->save();

        //withdrawBillVat
        $transaction = new Transaction;
        $transaction->user_id     = $this->bill->user_id;
        $transaction->bill_id     = $this->bill->id;
        $transaction->type        = 'debit';
        $transaction->amount      = $this->bill->payment_fees_vat;
        $transaction->reference   = $this->bill->number;
        $transaction->description = 'VAT - Transaction Processing';
        $transaction->transaction_source = 'vat';
        $transaction->save();

        //withdrawBillFees
        $transaction = new Transaction;
        $transaction->user_id     = $this->bill->user_id;
        $transaction->bill_id     = $this->bill->id;
        $transaction->type        = 'debit';
        $transaction->amount      = $this->bill->payment_fees;
        $transaction->reference   = $this->bill->number;
        $transaction->description = 'Fee - Transaction Processing';
        $transaction->transaction_source = 'fees';
        $transaction->save();
    }
}
