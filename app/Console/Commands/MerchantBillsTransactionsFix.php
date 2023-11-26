<?php

namespace App\Console\Commands;

use App\Exports\MissingTransactionsSummaryExport;
use App\Mail\MissingTransactionsSummaryMail;
use App\Models\Bill;
use App\Models\PaymentLog;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class MerchantBillsTransactionsFix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merchants:bills-transactions-fix {--user_id=} {--from=} {--to=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for get paid and refunded bills and review transactions and insert missing types of transactions. you can get bills for merchant or in period optional';

    const PAYMENT_TRANSACTIONS = ['bill', 'fees', 'vat', 'surebills_fees', 'surebills_vat'];
    const CHANNEL_TRANSACTIONS = ['channel_fees', 'channel_vat'];
    const REFUND_TRANSACTIONS = ['refund'];

    private $inserted_transactions_summary = [];
    
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
        $user_id = $this->option('user_id');
        $from = $this->option('from');
        $to = $this->option('to');

        $merchantBills = $this->getBills($user_id, $from, $to);

        $billsCount = count($merchantBills);
        
        $this->info($billsCount.' bills found');

        if($this->confirm('Do you wish to review this bills ?')){
            if($billsCount > 0){
                $chunked_bills = array_chunk($merchantBills, 100);
                foreach($chunked_bills as $key => $bills){
                    foreach($bills as $bkey => $bill_id){
                        $this->info('Round '.($bkey+1)*($key+1));
                        $bill = Bill::find($bill_id);

                        if(!$bill){
                            $error = 'Bill ('.$bill_id.') Not found';
                            $this->error($error);
                            Log::channel('bills_missing_transactions_fixing_summary')->error($error);
                            continue;
                        }

                        if(in_array($bill->status, ['paid', 'refunded'])){
                            $requiredTransactions = self::PAYMENT_TRANSACTIONS;

                            if($bill->status == 'refunded'){
                                $requiredTransactions = array_merge(self::PAYMENT_TRANSACTIONS, self::REFUND_TRANSACTIONS);
                            }
                            
                            if($bill->application_id != null && $bill->application->channel_id != null){
                                $requiredTransactions = array_merge($requiredTransactions, self::CHANNEL_TRANSACTIONS);
                            }

                            $paymentLog = $this->checkBillPaymentLog($bill_id, $bill->status);

                            if($paymentLog){
                                $currentTransactions = $this->getBillTransactionsSources($bill_id);
                                
                    
                                $missingTransactions = array_diff($requiredTransactions, $currentTransactions);
                                
                                if(empty($missingTransactions)){
                                    $this->info('bill ('.$bill_id.') is '.$bill->status.' and have not missing Transactions');
                                    continue;
                                }
                                
                                $this->info('bill ('.$bill_id.') is '.$bill->status.' and have not the below transactions');
                                foreach($missingTransactions as $tkey => $transaction){
                                    $this->line($tkey.'-'.$transaction);
                                }

                                $this->insertMissingTransactions($missingTransactions, $bill, $paymentLog);
                                
                                $billTransactions = Transaction::where('bill_id', $bill_id)->get();

                                $this->info($billTransactions);
                            }else{
                                $error = 'Bill ('.$bill_id.') have not success payment logs';
                                $this->info($error);
                                Log::channel('bills_missing_transactions_fixing_summary')->error($error);
                            }
                        }else{
                            $error = 'Bill ('.$bill_id.') is '.$bill->status;
                            $this->info($error);
                            Log::channel('bills_missing_transactions_fixing_summary')->error($error);
                        }
                    }
                }
                $this->info("All Bills review and fixing succeefully. Wait transactions Summary exportation!");
                if(!empty($this->inserted_transactions_summary)){
                    $this->exportTransactionsSummary();
                    $this->info("Exportation File ready to download on email.");
                }
            }else{
                $this->info("Thank You no changes for any bill");
            }
        }
    }
    
    private function getBills($user_id = null, $from = null, $to = null){
        $merchantBills = DB::table('bills')
        ->leftJoin('transactions', 'bills.id', '=', 'transactions.bill_id')
        ->leftJoin('applications', 'bills.application_id', '=', 'applications.id')
        ->leftJoin('channels', 'applications.channel_id', '=', 'channels.id');
        
        $merchantBills = $merchantBills->select('bills.id as bid', DB::raw('count(transactions.bill_id) as trans'));
        
        $merchantBills = $merchantBills->whereIn('bills.status', ['paid', 'refunded']);

        if($user_id != null){
            $merchantBills = $merchantBills->where('bills.user_id', $user_id);
        }

        if($from != null){
            $merchantBills = $merchantBills->whereDate('bills.created_at', '>=', $from);
        }

        if($to != null){
            $merchantBills = $merchantBills->whereDate('bills.created_at', '<=', $to);
        }

        $merchantBillsBasic = clone $merchantBills;
        $merchantBillsBasic = $merchantBillsBasic->whereNull('applications.channel_id');
        $merchantBillsBasic = $merchantBillsBasic->groupBy('bid');
        $merchantBillsBasic = $merchantBillsBasic->having('trans', '<', 5);
        $merchantBillsBasic = $merchantBillsBasic->orderBy('trans');
        $merchantBillsBasic = $merchantBillsBasic->get()->toArray();
        
        
        $merchantBillsChannel = clone $merchantBills;
        $merchantBillsChannel = $merchantBillsChannel->whereNotNull('applications.channel_id');
        $merchantBillsChannel = $merchantBillsChannel->groupBy('bid');
        $merchantBillsChannel = $merchantBillsChannel->having('trans', '<', 7);
        $merchantBillsChannel = $merchantBillsChannel->orderBy('trans');
        $merchantBillsChannel = $merchantBillsChannel->get()->toArray();
        
        $merchantBillsArr = array_merge($merchantBillsBasic, $merchantBillsChannel);
        $merchantBillsArrIds = array_column($merchantBillsArr,'bid');
        
        return $merchantBillsArrIds;
    }

    private function checkBillPaymentLog($bill_id, $bill_status){
        if($bill_status == 'paid'){
            $transacion_type = 'PAYMENT';
        }elseif($bill_status == 'refunded'){
            $transacion_type = 'REFUND';
        }

        $paymentLog = PaymentLog::where('bill_id', $bill_id)
        ->whereJsonContains('results->transaction->type', $transacion_type)
        ->whereJsonContains('results->result', 'SUCCESS')
        ->whereJsonContains('results->response->gatewayCode', 'APPROVED')
        ->first();

        return $paymentLog ?? false;
    }

    private function getBillTransactionsSources($bill_id){
        $transactions_sources = Transaction::where('bill_id', $bill_id)->pluck('transaction_source')->toArray();

        return $transactions_sources;
    }

    private function insertMissingTransactions($missingTransactions, $bill, $log){
        $user = User::whereEmail('surebills@sura.com.sa')->first();
        $dash = $bill->customer_name ? '-' : '';
        
        foreach ($missingTransactions as $transaction) {
            switch ($transaction) {
                case 'bill':
                    $prefix = $bill->debit_note_bill_id ? 'DN' : 'Bill';

                    $transaction = new Transaction;
                    $transaction->user_id     = $bill->user_id;
                    $transaction->bill_id     = $bill->id;
                    $transaction->type        = 'credit';
                    $transaction->amount      = ($bill->total - $bill->channel_extra_amount - $bill->channel_extra_vat);
                    $transaction->reference   = $bill->number;
                    $transaction->description = $prefix.''.$bill->number .' '.$dash.' '. $bill->customer_name;
                    $transaction->auth_id     = $log->bank_transaction_id;
                    if ($log->payment_method == 'mastercard_applepay') {
                        $transaction->card_brand  = 'APPLEPAY - ' . $log->brand;
                        $transaction->card        = $log->card_number;
                    } else {
                        $transaction->card_brand  = $log->brand;
                        $transaction->card        = $log->card_number;
                    }
                    $transaction->transaction_source = 'bill';
                    $transaction->saveIfUnique();

                    $this->pushTransaction($transaction);
                    $this->line('bill transaction inserted');
                    break;

                case 'fees':
                    //withdrawBillFees
                    $transaction = new Transaction;
                    $transaction->user_id     = $bill->user_id;
                    $transaction->bill_id     = $bill->id;
                    $transaction->type        = 'debit';
                    $transaction->amount      = $bill->payment_fees;
                    $transaction->reference   = $bill->number;
                    $transaction->description = 'Fee - Transaction Processing';
                    $transaction->transaction_source = 'fees';
                    $transaction->saveIfUnique();

                    $this->pushTransaction($transaction);
                    $this->line('fees transaction inserted');
                    break;

                case 'vat':
                    //withdrawBillVat
                    $transaction = new Transaction;
                    $transaction->user_id     = $bill->user_id;
                    $transaction->bill_id     = $bill->id;
                    $transaction->type        = 'debit';
                    $transaction->amount      = $bill->payment_fees_vat;
                    $transaction->reference   = $bill->number;
                    $transaction->description = 'VAT - Transaction Processing';
                    $transaction->transaction_source = 'vat';
                    $transaction->saveIfUnique();

                    $this->pushTransaction($transaction);
                    $this->line('vat transaction inserted');
                    break;
                    
                case 'surebills_fees':
                    if($user && isset($bill->payment_surebills_fees)){
                        $fee_trans = new Transaction;
                        $fee_trans->user_id     = $user->id;
                        $fee_trans->bill_id     = $bill->id;
                        $fee_trans->type        = 'credit';
                        $fee_trans->amount      = $bill->payment_surebills_fees;
                        $fee_trans->reference   = $bill->number;
                        $fee_trans->description = 'Fee - Bill Number: '.$bill->number;
                        $fee_trans->transaction_source = 'surebills_fees';
                        $fee_trans->saveIfUnique();

                        $this->pushTransaction($fee_trans);
                        $this->line('surebills_fees transaction inserted');
                    }
                    else{
                        $error = 'surebills_fees transaction not inserted! surebill user (surebills@sura.com.sa) not found or bill ('.$bill->id.') payment surebills fees not calculated';
                        $this->error($error);
                        Log::channel('bills_missing_transactions_fixing_summary')->error($error);
                    }
                    break;

                case 'surebills_vat':
                    if($user && isset($bill->payment_surebills_fees_vat) ){
                        $vat_trans = new Transaction;
                        $vat_trans->user_id     = $user->id;
                        $vat_trans->bill_id     = $bill->id;
                        $vat_trans->type        = 'credit';
                        $vat_trans->amount      = $bill->payment_surebills_fees_vat;
                        $vat_trans->reference   = $bill->number;
                        $vat_trans->description = 'Vat - Bill Number: '.$bill->number;
                        $vat_trans->transaction_source = 'surebills_vat';
                        $vat_trans->saveIfUnique();

                        $this->pushTransaction($vat_trans);
                        $this->line('surebills_vat transaction inserted');
                    }else{
                        $error = 'surebills_vat transaction not inserted! surebill user (surebills@sura.com.sa) not found or bill ('.$bill->id.') payment surebills fees vat not calculated';
                        $this->error($error);
                        Log::channel('bills_missing_transactions_fixing_summary')->error($error);
                    }
                    break;

                case 'channel_fees':
                    if(isset($bill->application) && isset($bill->application->channel)){
                        $fee_trans = new Transaction;
                        $fee_trans->user_id     = $bill->application->channel->user_id;
                        $fee_trans->bill_id     = $bill->id;
                        $fee_trans->type        = 'credit';
                        $fee_trans->amount      = $bill->payment_channel_fees;
                        $fee_trans->reference   = $bill->number;
                        $fee_trans->description = 'Fee - Channel: '.$bill->application->channel->name;
                        $fee_trans->transaction_source = 'channel_fees';
                        $fee_trans->saveIfUnique();

                        $this->pushTransaction($fee_trans);
                        $this->line('channel_fees transaction inserted');
                    }else{
                        $error = 'channel_fees transaction not inserted! this bill ('.$bill->id.') application not found or application does not have channel';
                        $this->error($error);
                        Log::channel('bills_missing_transactions_fixing_summary')->error($error);
                    }
                    break;
                    
                case 'channel_vat':
                    if(isset($bill->application) && isset($bill->application->channel)){
                        $vat_trans = new Transaction;
                        $vat_trans->user_id     = $bill->application->channel->user_id;
                        $vat_trans->bill_id     = $bill->id;
                        $vat_trans->type        = 'credit';
                        $vat_trans->amount      = $bill->payment_channel_fees_vat;
                        $vat_trans->reference   = $bill->number;
                        $vat_trans->description = 'Vat - Channel: '.$bill->application->channel->name;
                        $vat_trans->transaction_source = 'channel_vat';
                        $vat_trans->saveIfUnique();

                        $this->pushTransaction($vat_trans);
                        $this->line('channel_vat transaction inserted');
                    }
                    else{
                        $error = 'channel_vat transaction not inserted! this bill ('.$bill->id.') application not found or application does not have channel';
                        $this->error($error);
                        Log::channel('bills_missing_transactions_fixing_summary')->error($error);
                    }
                    break;

                case 'refund':
                    $order_max = Transaction::where('bill_id', $bill->id)->max('order');

                    $transaction = new Transaction;
                    $transaction->user_id     = $bill->user_id;
                    $transaction->bill_id     = $bill->id;
                    $transaction->type        = 'debit';
                    $transaction->amount      = $bill->total;
                    $transaction->reference   = $bill->number;
                    $transaction->description = 'REFUND Bill ' . $bill->number .' '.$dash.' '. $bill->customer_name;
                    $transaction->auth_id     = $log->bank_transaction_id;
                    $transaction->card_brand  = $log->brand;
                    $transaction->card        = $log->card_number;
                    $transaction->transaction_source = 'refund';
                    $transaction->order = $order_max+1;
                    $transaction->save();

                    $this->pushTransaction($transaction);
                    $this->line('refund transaction inserted');
                    break;
                
                default:
                    # code...
                    break;
            }
        }
        $this->info('All missing transactions inserted');
    }

    private function pushTransaction($transaction){
        $this->inserted_transactions_summary[] = [
            $transaction->id,
            $transaction->bill_id,
            $transaction->type,
            $transaction->transaction_source,
            $transaction->amount,
            $transaction->user_id,
        ];

        return true;
    }

    private function exportTransactionsSummary(){
        $file_name = 'fixing_commands_files/Missing_Transactions_Summary.xlsx';
        if(Excel::store(new MissingTransactionsSummaryExport($this->inserted_transactions_summary), $file_name , 'public')){
            $emails = ['mzain@sure.com.sa'];
            if(count($emails)){
                foreach ($emails as $email) {
                    $message = (new MissingTransactionsSummaryMail($file_name))->onQueue(env('EMAILS_QUEUE'));
                    Mail::to($email)->queue($message);
                }
            }
        }
    }
}
