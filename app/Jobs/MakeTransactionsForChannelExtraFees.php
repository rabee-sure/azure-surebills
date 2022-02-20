<?php

namespace App\Jobs;

use App\Models\Bill;
use App\Models\Transaction;

class MakeTransactionsForChannelExtraFees
{
    protected $bill;

    protected $log;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Bill $bill, $payment_log)
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
        $channel = $this->bill->application->channel ?? null;
        if(isset($this->bill->application) && isset($channel)){

            $percentage = $channel->getPercentage($this->log);
            $fixed = $channel->getFixed($this->log);

            $total_amount = $this->bill->channel_extra_amount + $this->bill->channel_extra_vat;
            $channel_extra_amount_fees = ($total_amount * ($percentage / 100)) + $fixed;
            $channel_extra_amount_vat = $total_amount * (Transaction::VAT_PERCENTAGE / 100);

            $fee_trans = new Transaction;
            $fee_trans->user_id     = $channel->user_id;
            $fee_trans->bill_id     = $this->bill->id;
            $fee_trans->type        = 'credit';
            $fee_trans->amount      = $total_amount;
            $fee_trans->reference   = $this->bill->number;
            $fee_trans->description = 'channel extra - Channel: '.$channel->name;
            $fee_trans->transaction_source = 'channel_extra_amount';
            $fee_trans->save();

            $vat_trans = new Transaction;
            $vat_trans->user_id     = $channel->user_id;
            $vat_trans->bill_id     = $this->bill->id;
            $vat_trans->type        = 'debit';
            $vat_trans->amount      = $channel_extra_amount_vat;
            $vat_trans->reference   = $this->bill->number;
            $vat_trans->description = 'channel extra Fees - Channel: '.$channel->name;
            $vat_trans->transaction_source = 'channel_extra_amount_vat';
            $vat_trans->save();

            $vat_trans = new Transaction;
            $vat_trans->user_id     = $channel->user_id;
            $vat_trans->bill_id     = $this->bill->id;
            $vat_trans->type        = 'debit';
            $vat_trans->amount      = $channel_extra_amount_fees;
            $vat_trans->reference   = $this->bill->number;
            $vat_trans->description = 'channel extra Vat - Channel: '.$channel->name;
            $vat_trans->transaction_source = 'channel_extra_amount_fees';
            $vat_trans->save();

        }
    }

    public static function dispatch($bill, $payment_log)
    {
        $job = new self($bill, $payment_log);
        $job->handle();
    }
}
