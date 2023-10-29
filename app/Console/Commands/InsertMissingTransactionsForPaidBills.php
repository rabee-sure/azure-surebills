<?php

namespace App\Console\Commands;

use App\Events\BillPaid;
use App\Models\Bill;
use App\Models\PaymentLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class InsertMissingTransactionsForPaidBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:fix_missing_transactions {--from=} {--to=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for get paid bills which dosnot have payment transactions and make sure from payment log and insert missing transactions for it';

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
        $from = $this->option('from');
        $to = $this->option('to');
        
        $paidBills = Bill::where('status', 'paid')->doesnthave('transactions');

        if($from != null){
            $paidBills = $paidBills->whereDate('created_at', '>=', $from);
        }

        if($to != null){
            $paidBills = $paidBills->whereDate('created_at', '<=', $to);
        }

        $paidBills = $paidBills->orderBy('created_at')->pluck('id')->toArray();

        $billsCount = count($paidBills);
        
        $this->info($billsCount.' bills found missing transactions');

        if($this->confirm('Do you wish to fix this bills ?')){
            if($billsCount > 0){
                $chunked_bills = array_chunk($paidBills, 100);
                foreach($chunked_bills as $key => $bills){
                    foreach($bills as $bill_id){
                        // check for bill paymentLog
                        $billPaymentLog = PaymentLog::where('bill_id', $bill_id)
                        ->whereIn('payment_method', ['mastercard_pay', 'hyperpay_applepay', 'mastercard_applepay', 'stc_pay'])
                        ->where('webhook_response_received', 1)
                        ->where('is_failure', 0)
                        ->whereJsonContains('results->transaction->type', 'PAYMENT')
                        ->whereJsonContains('results->result', 'SUCCESS')
                        ->whereJsonContains('results->response->gatewayCode', 'APPROVED')
                        ->orderBy('created_at', 'Desc')
                        ->first();

                        if($billPaymentLog != null){
                            $bill = Bill::find($bill_id);
                            event(new BillPaid($bill, $billPaymentLog));
                            $this->line("BillPaid Event fire for bill ".$bill_id);
                        }else{
                            Log::channel('bills_transactions_fix')->info("Faild bill id: ", [$bill_id]);
                            $this->line("BillPaid Event not fire for bill ".$bill_id);
                        }
                    }
                }
                
                $this->info("All correct paid Bills missing inserted succeefully! you can view faild bills in Bill transactions fix log file");
            }
        }
    }
}
