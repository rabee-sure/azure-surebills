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
use Illuminate\Support\Facades\Log;

class MakeTransactionsForSureBills
{
    use Dispatchable, SerializesModels;

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
        $user = User::whereEmail('surebills@sura.com.sa')->first();
        Log::error('MakeTransactionsForSureBills', ['user' => $user]);
        Log::error('MakeTransactionsForSureBills', ['surebills_fees' => $this->bill->payment_surebills_fees]);
        Log::error('MakeTransactionsForSureBills', ['surebills_fees_vat' => $this->bill->payment_surebills_fees_vat]);

        if($user && isset($this->bill->payment_surebills_fees) && isset($this->bill->payment_surebills_fees_vat) ){

            $fee_trans = new Transaction;
            $fee_trans->user_id     = $user->id;
            $fee_trans->bill_id     = $this->bill->id;
            $fee_trans->type        = 'credit';
            $fee_trans->amount      = $this->bill->payment_surebills_fees;
            $fee_trans->reference   = $this->bill->number;
            $fee_trans->description = 'Fee - Bill Number: '.$this->bill->number;
            $fee_trans->transaction_source = 'surebills_fees';
            $fee_trans->saveIfUnique();

            $vat_trans = new Transaction;
            $vat_trans->user_id     = $user->id;
            $vat_trans->bill_id     = $this->bill->id;
            $vat_trans->type        = 'credit';
            $vat_trans->amount      = $this->bill->payment_surebills_fees_vat;
            $vat_trans->reference   = $this->bill->number;
            $vat_trans->description = 'Vat - Bill Number: '.$this->bill->number;
            $vat_trans->transaction_source = 'surebills_vat';
            $vat_trans->saveIfUnique();
        }
    }
}
