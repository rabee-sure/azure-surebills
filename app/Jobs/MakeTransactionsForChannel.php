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

class MakeTransactionsForChannel
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
        if(isset($this->bill->application) && isset($this->bill->application->channel)){
            $fee_trans = new Transaction;
            $fee_trans->user_id     = $this->bill->application->channel->user_id;
            $fee_trans->bill_id     = $this->bill->id;
            $fee_trans->type        = 'credit';
            $fee_trans->amount      = $this->bill->payment_channel_fees;
            $fee_trans->reference   = $this->bill->number;
            $fee_trans->description = 'Fee - Channel: '.$this->bill->application->channel->name;
            $fee_trans->transaction_source = 'channel_vat';
            $fee_trans->save();

            $vat_trans = new Transaction;
            $vat_trans->user_id     = $this->bill->application->channel->user_id;
            $vat_trans->bill_id     = $this->bill->id;
            $vat_trans->type        = 'credit';
            $vat_trans->amount      = $this->bill->payment_channel_fees_vat;
            $vat_trans->reference   = $this->bill->number;
            $vat_trans->description = 'Vat - Channel: '.$this->bill->application->channel->name;
            $vat_trans->transaction_source = 'channel_fees';
            $vat_trans->save();
        }
    }
}
