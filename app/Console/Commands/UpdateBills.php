<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\Bill;
use App\Events\BillStatusUpdated;
use App\Events\UserCreated;
use App\Jobs\MakeTransactionsForSureBills;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UpdateBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Bills';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->fixChannelFeesVat();
        // $this->updatePricing();
    }


    protected function fixChannelFeesVat()
    {
        Bill::whereHas('application')->paid()->chunk(500, function($bills)
        {
            foreach ($bills as $bill) {
                $trans = $bill->transactions()->whereIn('transaction_source', ['channel_vat', 'channel_fees'])->get();

                if($trans->count() == 2){
                    $o_vat = $trans->where('transaction_source', 'channel_vat')->first();
                    $o_fees = $trans->where('transaction_source', 'channel_fees')->first();
                    if($o_vat->amount > $o_fees->amount){
                        $o_vat->transaction_source = 'channel_fees';
                        $o_vat->save();                        

                        $o_fees->transaction_source = 'channel_vat';
                        $o_fees->save();
                        $this->info("fix Channel Fees Vat in bill id: {$bill->id}");
                    }
                }
            }
        });
    }

    protected function updatePricing()
    {
        Bill::where('payment_surebills_fees_vat', null)->paid()->chunk(500, function($bills)
        {
            foreach ($bills as $bill) {
                $payment_log = $bill->success_payment;

                if($payment_log){
                    $bill = $bill;
                    $percentage = $bill->getPercentage($payment_log);
                    $fixed = $bill->getFixed($payment_log);

                    $bill->settled = false;
                    $bill->pricing_fees_details = $percentage.'%,'. $fixed;
                    $bill->payment_fees = $bill->total * ($percentage / 100) + $fixed;
                    $bill->payment_fees_vat = $bill->payment_fees * (Transaction::VAT_PERCENTAGE / 100);

                    $payment_surebills = $this->paymentSurebillsFees($bill, $payment_log);
                    $bill->payment_surebills_fees = $payment_surebills['fees'];
                    $bill->payment_surebills_fees_vat = $payment_surebills['fees_vat'];

                    $payment_channel = $this->paymentChannelFees($bill, $payment_log);
                    $bill->payment_channel_fees = $payment_channel['fees'];
                    $bill->payment_channel_fees_vat = $payment_channel['fees_vat'];
                    $bill->save();

                    $this->info("update bill id: {$bill->id}");
                }
            }
        });
    } 

    protected function paymentSurebillsFees($bill, $log):Array
    {
        if(isset($bill->application) && isset($bill->application->channel)){
            $percentage = $bill->getPercentage($log, true);
            $fixed = $bill->getFixed($log, true);

            $payment_fees = $bill->total * ($percentage / 100) + $fixed;
            $payment_fees_vat = $payment_fees * (Transaction::VAT_PERCENTAGE / 100);
        }else{
            $payment_fees = $bill->payment_fees;
            $payment_fees_vat = $bill->payment_fees_vat;
        }

        return [
            'fees' => $payment_fees,
            'fees_vat' => $payment_fees_vat,
        ];
    }

    protected function paymentChannelFees($bill, $log):Array
    {
        if(isset($bill->application) && isset($bill->application->channel)){
            $percentage = $bill->getPercentage($log, true);
            $fixed = $bill->getFixed($log, true);

            $p_fees = $bill->total * ($percentage / 100) + $fixed;
            $p_fees_vat = $p_fees * (Transaction::VAT_PERCENTAGE / 100);

            $payment_fees = $bill->payment_fees - $p_fees;
            $payment_fees_vat = $bill->payment_fees_vat - $p_fees_vat;
        }else{
            $payment_fees = null;
            $payment_fees_vat = null;
        }

        return [
            'fees' => $payment_fees,
            'fees_vat' => $payment_fees_vat,
        ];
    }
}
