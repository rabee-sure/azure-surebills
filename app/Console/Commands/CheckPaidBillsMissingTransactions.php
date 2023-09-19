<?php

namespace App\Console\Commands;

use App\Events\BillPaid;
use App\Models\Bill;
use App\Models\PaymentLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckPaidBillsMissingTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:check_paid_bills_missing_transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for check paid bills missing transactions and insert it';

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
        $date = date('Y-m-d', strtotime(' -2 day'));

        $paidBills = Bill::where('status', 'paid')->doesnthave('transactions')->whereDate('created_at', $date);

        $paidBills = $paidBills->orderBy('created_at')->pluck('id')->toArray();

        $billsCount = count($paidBills);

        $this->info($billsCount.' bills found missing transactions');

        if($billsCount > 0){
            Log::channel('paid_bills_transactions_fixed')->error("Check date: ", [$date]);
            Log::channel('paid_bills_transactions_not_fixed')->error("Check date: ", [$date]);
            $chunked_bills = array_chunk($paidBills, 100);
            foreach($chunked_bills as $key => $bills){
                foreach($bills as $bill_id){
                    // check for bill paymentLog
                    $billPaymentLog = PaymentLog::where('bill_id', $bill_id)
                    ->whereIn('payment_method', ['mastercard_pay', 'hyperpay_applepay', 'mastercard_applepay', 'stc_pay'])
                    ->where('webhook_response_received', 1)
                    ->where('is_failure', 0)
                    ->where(function($query){
                        $query->where('results', 'like', '%"result":"SUCCESS"%')
                        ->orWhere('results', 'like', '%"result": "SUCCESS"%');
                    })
                    ->orderBy('created_at', 'Desc')
                    ->first();

                    if($billPaymentLog != null){
                        $bill = Bill::find($bill_id);
                        event(new BillPaid($bill, $billPaymentLog));
                        Log::channel('paid_bills_transactions_fixed')->error("bill id: ", [$bill_id]);
                        $this->line("BillPaid Event fire for bill ".$bill_id);
                    }else{
                        Log::channel('paid_bills_transactions_not_fixed')->error("bill id: ", [$bill_id]);
                        $this->line("BillPaid Event not fire for bill ".$bill_id);
                    }
                }
            }

            $this->info("All correct paid Bills missing inserted succeefully! you can view faild bills in Bill transactions fix log file");
        }
    }
}
