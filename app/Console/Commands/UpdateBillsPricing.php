<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\Bill;
use App\Events\BillStatusUpdated;
use App\Jobs\MakeTransactionsForSureBills;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UpdateBillsPricing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:pricing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Bills pricing';

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
        $this->updateBillsIfDontHavePricing();
    }

    protected function updateBillsIfDontHavePricing()
    {
        Bill::paid()->chunk(500, function($bills)
        {
            foreach ($bills as $bill) {
                $payment_log = $bill->success_payment;

                if($payment_log){
                    $bill = $bill;
                    $percentage = $bill->getPercentage($payment_log);
                    $fixed = $bill->getFixed($payment_log);

                    $bill->pricing = [
                        'type' => $this->getType($bill),
                        'fees_percentage' => $percentage,
                        'fees_fixed' => $fixed,
                        'surebills_fees_percentage' => $this->getType($bill) == 'channel' ?$bill->getPercentage($payment_log, true) : $percentage,
                        'surebills_fees_fixed' =>  $this->getType($bill) == 'channel' ? $bill->getFixed($payment_log, true) : $fixed,
                        'vat_percentage' => Transaction::VAT_PERCENTAGE,
                        'channel_fees_percentage' => $this->getType($bill) == 'channel' ?  $percentage - $bill->getPercentage($payment_log, true) : null,
                        'channel_fees_fixed' =>  $this->getType($bill) == 'channel' ? $fixed - $bill->getFixed($payment_log, true) : null,
                    ];

                    $bill->save();

                    $this->info("update bill id: {$bill->id}");
                }
            }
        });
    }

    protected function getType($bill)
    {
        if($bill->application_id && isset($bill->application)){
            if($bill->application->channel_id){
                return 'channel';
            }
            else{
                return 'application';
            }
        }else{
            return 'user';
        }
    }
}
