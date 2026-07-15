<?php

namespace App\Console\Commands;

use App\Exports\MerchantsBalanceExport;
use App\Mail\MerchantsBalancesReportMail;
use App\Models\Transaction;
use App\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class UnMatchingMerchantsTransfer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merchants:transfer_balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'command for detect merchants who their balance not match with transfer amount and their transactions seetled not correct';

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
        ini_set('memory_limit','4096M');
        $activeUsers = DB::table('users')->whereNull('store_main_user_id')->where('verified', 1)->select('id')->get()->pluck('id')->toArray();

        $results = [];

        if(!empty($activeUsers)){
            foreach($activeUsers as $user){
                $raw = [];

                $this->info("start with user {$user}");

                $balance_obj = Transaction::where('user_id', $user)->select(DB::raw("SUM(CASE WHEN type  = 'credit' THEN amount ELSE 0 END) AS credit_total,SUM(CASE WHEN type  = 'debit' THEN amount ELSE 0 END) AS debit_total"))->first();
                $this->line('Total credit = '.$balance_obj->credit_total);
                $this->line('Total debit = '.$balance_obj->debit_total);
                $balance = $balance_obj->credit_total - $balance_obj->debit_total;
                $this->line('Balance = '.$balance);

                $count_transfers_transactions = Transaction::where('user_id', $user)->where('transaction_source', '=', "transfer")->count();
                $this->line('Count transfers = '.$count_transfers_transactions);

                $sum_transfers_transactions = Transaction::where('user_id', $user)->where('transaction_source', '=', "transfer")->sum('amount');
                $this->line('Total transfers amount = '.$sum_transfers_transactions);

                $count_completed_transfers = Transfer::where('user_id', $user)->where('status', 'completed')->count();
                $this->line('Count completed transfers = '.$count_completed_transfers);

                $sum_completed_transfers = Transfer::where('user_id', $user)->where('status', 'completed')->sum('amount');
                $this->line('Total completed transfers amount = '.$sum_completed_transfers);

                $count_pending_transfers = Transfer::where('user_id', $user)->whereIn('status', ['pending', 'send_to_sps'])->count();
                $this->line('Count pending transfers = '.$count_pending_transfers);

                $sum_pending_transfers = Transfer::where('user_id', $user)->whereIn('status', ['pending', 'send_to_sps'])->sum('amount');
                $this->line('Total pending transfers amount = '.$sum_pending_transfers);

                $count_unsettled_transactions = Transaction::where('user_id', $user)->where('pending_settled', false)->where('settled', false)->where('transaction_source', '!=', "transfer")->count();
                $this->line('Count unsettled transactions = '.$count_unsettled_transactions);

                $sum_unsettled_transactions = Transaction::where('user_id', $user)->where('pending_settled', false)->where('settled', false)->where('transaction_source', '!=', "transfer")->sum('amount');
                $this->line('Total unsettled transactions amount = '.$sum_unsettled_transactions);

                if($balance != ($sum_unsettled_transactions + $sum_pending_transfers)){
                    array_push($raw, $user);
                    array_push($raw, $balance_obj->credit_total);
                    array_push($raw, $balance_obj->debit_total);
                    array_push($raw, $balance);
                    array_push($raw, $count_transfers_transactions);
                    array_push($raw, $sum_transfers_transactions);
                    array_push($raw, $count_completed_transfers);
                    array_push($raw, $sum_completed_transfers);
                    array_push($raw, $count_pending_transfers);
                    array_push($raw, $sum_pending_transfers);
                    array_push($raw, $count_unsettled_transactions);
                    array_push($raw, $sum_unsettled_transactions);
                    array_push($results, $raw);
                }
            }
        }

        if(!empty($results)){
            $file_name = 'merchants/balance.xlsx';
            if(Excel::store(new MerchantsBalanceExport($results), $file_name)){
                $emails = ['mzain@sure.com.sa'];
                if(count($emails)){
                    foreach ($emails as $email) {
                        $message = (new MerchantsBalancesReportMail($file_name))->onQueue(env('EMAILS_QUEUE'));
                        Mail::to($email)->queue($message);
                    }
                }
            }
        }
    }
}
