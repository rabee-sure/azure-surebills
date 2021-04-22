<?php

namespace App\Jobs;

use App\Models\Bill;
use App\Models\PaymentLog;
use App\Models\Transaction;
use App\Services\BillService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PartialRefundTransactionsForChannel
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
        $payment_channel = BillService::getPaymentChannelFees($this->bill, $this->amount);
        $order_max = Transaction::where('bill_id', $this->bill->id)->max('order');

        if(isset($this->bill->application) && isset($this->bill->application->channel)){
            $fee_trans = new Transaction;
            $fee_trans->user_id     = $this->bill->application->channel->user_id;
            $fee_trans->bill_id     = $this->bill->id;
            $fee_trans->type        = 'debit';
            $fee_trans->amount      = $payment_channel['fees'];
            $fee_trans->reference   = $this->bill->number;
            $fee_trans->description = 'PARTIAL REFUND Fee - Channel: '.$this->bill->application->channel->name;
            $fee_trans->transaction_source = 'refund';
            $fee_trans->order = $order_max+1;
            $fee_trans->save();

            $vat_trans = new Transaction;
            $vat_trans->user_id     = $this->bill->application->channel->user_id;
            $vat_trans->bill_id     = $this->bill->id;
            $vat_trans->type        = 'debit';
            $vat_trans->amount      = $payment_channel['fees_vat'];
            $vat_trans->reference   = $this->bill->number;
            $vat_trans->description = 'PARTIAL REFUND Vat - Channel: '.$this->bill->application->channel->name;
            $vat_trans->transaction_source = 'refund';
            $vat_trans->order = $order_max+2;
            $vat_trans->save();
        }
    }
}
