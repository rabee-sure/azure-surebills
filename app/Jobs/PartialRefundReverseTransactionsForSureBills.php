<?php

namespace App\Jobs;

use App\Models\Bill;
use App\Models\Transaction;
use App\Models\User;
use App\Services\BillService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PartialRefundReverseTransactionsForSureBills
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
        return null;
        $order_max = Transaction::where('bill_id', $this->bill->id)->max('order');

        $payment_surebills = BillService::getPaymentSurebillsFees($this->bill, $this->amount);

        $user = User::whereEmail('surebills@sura.com.sa')->first();

        $fee_trans = new Transaction;
        $fee_trans->user_id     = $user->id;
        $fee_trans->bill_id     = $this->bill->id;
        $fee_trans->type        = 'credit';
        $fee_trans->amount      = $payment_surebills['fees'];
        $fee_trans->reference   = $this->bill->number;
        $fee_trans->description = 'PARTIAL REFUND Fee - Bill Number: '.$this->bill->number.' - Reversed Transaction For Failed Refund';
        $fee_trans->transaction_source = 'refund';
        $fee_trans->order = $order_max+1;
        $fee_trans->save();

        $vat_trans = new Transaction;
        $vat_trans->user_id     = $user->id;
        $vat_trans->bill_id     = $this->bill->id;
        $vat_trans->type        = 'credit';
        $vat_trans->amount      = $payment_surebills['fees_vat'];
        $vat_trans->reference   = $this->bill->number;
        $vat_trans->description = 'PARTIAL REFUND Vat - Bill Number: '.$this->bill->number.' - Reversed Transaction For Failed Refund';
        $vat_trans->transaction_source = 'refund';
        $vat_trans->order = $order_max+1;
        $vat_trans->save();
    }
}
