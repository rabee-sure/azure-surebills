<?php

namespace App\Console\Commands;

use App\Events\BillStatusUpdated;
use App\Events\UserCreated;
use App\Jobs\MakeTransactionsForSureBills;
use App\Models\Application;
use App\Models\Bill;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use App\Services\TransferService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Bills';

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
        // $transfers = Transfer::all();
        $transfers = Transfer::whereIn('id', [156])->get();
        foreach ($transfers as $key => $transfer) {
            
            $bank = $transfer->bank;
            $user = $transfer->user;
            $bills = $transfer->bills;
            $bill_ids = $bills->pluck('id');

            dd($user->balance);
            $transactions = Transaction::whereIn('bill_id', $bill_ids)
                    ->where('user_id', $user->id)
                    ->count();
            dd($transactions);

            $this->testTotoalDue($bill_ids);
            // $this->testBeforTransfer($transfer, $user);
            if($transfer->status == 'pending'){
                $this->testSetteldBills($bill_ids);
            }
            $this->testBillsInAntherTransfer($bill_ids,  $user);
        }

    }


    protected function testTotoalDue($ids)
    {
        $this->line("check if trafer all bills amount == total_due ");
        Bill::whereIn('id', $ids)->chunk(500, function($bills)
        {
            $ams = 0;
            $total_dues = 0;
            foreach ($bills as $bill) {
                $total_due =  round($bill->total - $bill->payment_fees - $bill->payment_fees_vat, 2);
                $transactions = Transaction::whereIn('bill_id',  [$bill->id])
                    ->where('user_id', $bill->user->id)
                    ->orderBy('created_at', 'ASC')
                    ->orderBy('receipt', 'ASC')
                    ->get();
                $am =  round($transactions->where('type', 'credit')->sum('amount') - $transactions->where('type', 'debit')->sum('amount'), 2);
                 $ams += $am;
                 $total_dues += $total_due;
                if($total_due == $am){
                    // $this->info("bill id: {$bill->id} - {$total_due} - {$am}");
                }elseif( $am - $total_due > 1 ||   $total_due - $am > 1){
                    $this->error("bill id: {$bill->id} - {$total_due} - {$am}");
                }else{
                    // $this->error("bill id: {$bill->id} - {$total_due} - {$am}");
                }
            }
            $diff = $total_dues - $ams;
                $this->line("{$total_dues} - {$ams} - diff {$diff}");
        });
    }

    protected function testSetteldBills($ids)
    {
        $this->line("check transfer have SETTELD bill ...");
        $bills_settled = Bill::whereIn('id', $ids)->settled()->get();
        if (count($bills_settled)) {
            foreach ($bills_settled as $bill) {
               $this->error("SETTELD bill id: {$bill->id} ");
            }
        }else{
            $this->info("this transfer dont have SETTELD bill");
        }
    }

    protected function testBeforTransfer($transfer, $user)
    {
        $last_transfer_transaction = $user->lastTransferTransaction();    
        // dd($last_transfer_transaction->id);
        $transactions = Transaction::where('user_id', $user->id)
            ->whereDate('created_at', '>', $last_transfer_transaction->created_at)
            ->get();

        $deposits = $transactions->where('type', 'credit')->sum('amount');
        $withdraws = $transactions->where('type', 'debit')->sum('amount');
        $balance = $deposits - $withdraws;
        dd($balance);
    }


    protected function testBillsInAntherTransfer($ids, $user)
    {
        $transfers = $user->transfers->pluck('id')->toArray();

        $this->line("check if trafer bills In Anther Transfer");
        $bills = Bill::whereIn('id', $ids)->get();

        $duplicate = 0;
        $bills_duplicate_ids = [];
        $dupl_transfer_id = [];
        foreach ($bills as $bill) {
            $bill_transfers = DB::table('bill_transfer')->where('bill_id', $bill->id)->get();
            if(count($bill_transfers) > 1 ){
                $ids = array_intersect($transfers, $bill_transfers->pluck('transfer_id')->toArray());
                $dupl_transfer_id =  array_merge($dupl_transfer_id, $ids);
                if(count($ids) > 1){
                    $duplicate += 1;
                    $bills_duplicate_ids[] = $bill->id;
                    $this->error("bill id: {$bill->id}");
                }
            }
        }

        $dupl_bills = Bill::whereIn('id', $bills_duplicate_ids)->get();
        $diff_balance = TransferService::getAmount($dupl_bills, $user);
        $this->line("transfer ".implode('-', array_unique($dupl_transfer_id))." bills count ".$bills->count() . " | duplicate bills count: ". $duplicate." | diff balance: ".$diff_balance);
    }

    protected function getBalanceFromBills($ids)
    {

    }
}
