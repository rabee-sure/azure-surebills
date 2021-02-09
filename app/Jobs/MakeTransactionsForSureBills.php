<?php

namespace App\Jobs;

use App\Models\Bill;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MakeTransactionsForSureBills implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        return null;

        $user = User::whereEmail('surebills@sura.com.sa')->first();

        $fee_trans = new Transaction;
        $fee_trans->user_id     = $user->id;
        $fee_trans->bill_id     = $this->bill->id;
        $fee_trans->type        = 'credit';
        $fee_trans->amount      = $this->bill->payment_surebills_fees;
        $fee_trans->reference   = $this->bill->number;
        $fee_trans->description = 'Fee - Bill Number: '.$this->bill->number;
        $fee_trans->transaction_source = 'surebills_fees';
        $fee_trans->save();

        $vat_trans = new Transaction;
        $vat_trans->user_id     = $user->id;
        $vat_trans->bill_id     = $this->bill->id;
        $vat_trans->type        = 'credit';
        $vat_trans->amount      = $this->bill->payment_surebills_fees;
        $vat_trans->reference   = $this->bill->payment_surebills_fees_vat;
        $vat_trans->description = 'Vat - Bill Number: '.$this->bill->number;
        $vat_trans->transaction_source = 'surebills_vat';
        $vat_trans->save();
    }
}
