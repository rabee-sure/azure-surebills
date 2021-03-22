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

class UpdateBillsPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Bills payments';

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
        $this->updateBillsPayments();
    }

    protected function updateBillsPayments()
    {
        Bill::paid()->chunk(500, function($bills)
        {
            foreach ($bills as $bill) {
                $pricing = $bill->pricing;
                $bill->payment_fees = $this->getPaymentFees($bill , $pricing);
                $bill->payment_surebills_fees = $this->getSurebillsFees($bill , $pricing);
                $bill->payment_surebills_fees_vat = $this->getSurebillsFeesVat($bill , $pricing);
                $bill->payment_channel_fees = $this->getChannelFees($bill , $pricing);
                $bill->payment_channel_fees_vat = $this->getChannelFeesVat($bill , $pricing);
                $bill->save();
                $this->info("update bill id: {$bill->id}");
            }
        });
    }

    protected function getPaymentFees($bill , $pricing){
        return ($pricing['fees_percentage'] * $bill->total/100) + $pricing['fees_fixed'];
    }    

    protected function getSurebillsFees($bill , $pricing){
        return ($pricing['surebills_fees_percentage'] * $bill->total/100) + $pricing['surebills_fees_fixed'];
    }
    protected function getSurebillsFeesVat($bill , $pricing){
        return (($pricing['surebills_fees_percentage'] * $bill->total/100) + $pricing['surebills_fees_fixed'])*($pricing['vat_percentage']/100);
    }    
    protected function getChannelFees($bill , $pricing){
        return ($pricing['channel_fees_percentage'] != null) ? ($pricing['channel_fees_percentage'] * $bill->total/100) + $pricing['channel_fees_fixed']: null;
    }   

    protected function getChannelFeesVat($bill , $pricing){
        return ($pricing['channel_fees_percentage'] != null) ?(($pricing['channel_fees_percentage'] * $bill->total/100) + $pricing['channel_fees_fixed'])*($pricing['vat_percentage']/100) : null;

    }
}
