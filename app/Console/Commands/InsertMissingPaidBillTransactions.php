<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\PaymentLog;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Console\Command;

class InsertMissingPaidBillTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bill:insert_missing_transactions {bill_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for check paid bill transactions and insert missing transaction for it';

    const PAYMENT_TRANSACTIONS = ['bill', 'fees', 'vat', 'surebills_fees', 'surebills_vat'];
    const CHANNEL_TRANSACTIONS = ['channel_fees', 'channel_vat'];
    const REFUND_TRANSACTIONS = ['refund'];

    private $refunded_channel = 'aa760a6e-b135-45a4-a50d-cc05ef971732';
    private $refunded = '08a64fa7-2dd5-4a0b-914a-f8d78af34319';
    private $paid = '006c41cd-d797-4c48-9795-6e1822b4dd59';
    private $paid_channel = '07de47d5-7e2f-4397-961b-c8194bc3486c';
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
        $bill_id = $this->argument('bill_id');

        $bill = Bill::find($bill_id);

        if(!$bill){
            $this->error('Bill Not found');
            return;
        }

        if(in_array($bill->status, ['paid', 'refunded'])){
            $requiredTransactions = self::PAYMENT_TRANSACTIONS;

            if($bill->status == 'refunded'){
                $requiredTransactions = array_merge(self::PAYMENT_TRANSACTIONS, self::REFUND_TRANSACTIONS);
            }
            
            if($bill->application_id != null){
                $requiredTransactions = array_merge($requiredTransactions, self::CHANNEL_TRANSACTIONS);
            }

            $paymentLog = $this->checkBillPaymentLog($bill_id, $bill->status);

            if($paymentLog){
                $currentTransactions = $this->getBillTransaction($bill_id);
                
    
                $missingTransactions = array_diff($requiredTransactions, $currentTransactions);
                
                if(empty($missingTransactions)){
                    $this->info('this bill is '.$bill->status.' and have not missing Transactions');
                    return;
                }
                
                $this->info('this bill is '.$bill->status.' and have not the below transactions');
                foreach($missingTransactions as $key => $transaction){
                    $this->line($key.'-'.$transaction);
                }

                if($this->confirm('Do you want to insert missing transactoins for this bill ?')){
                    $this->insertMissingTransactions($missingTransactions, $bill, $paymentLog);
                    
                    $billTransactions = Transaction::where('bill_id', $bill_id)->get();

                    $this->info($billTransactions);
                }
            }else{
                $this->info('This bill have not success payment logs');
            }
        }else{
            $this->info('This bill is '.$bill->status);
        }
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

    private function getBillTransaction($bill_id){
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

                        $this->line('surebills_fees transaction inserted');
                    }
                    else{
                        $this->error('surebill user not found or bill payment surebills fees not calculated');
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

                        $this->line('surebills_vat transaction inserted');
                    }else{
                        $this->error('surebill user not found or bill payment surebills fees vat not calculated');
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

                        $this->line('channel_fees transaction inserted');
                    }else{
                        $this->error('this bill application not found or application does not have channel');
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

                        $this->line('channel_vat transaction inserted');
                    }
                    else{
                        $this->error('this bill application not found or application does not have channel');
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

                    $this->line('refund transaction inserted');
                    break;
                
                default:
                    # code...
                    break;
            }
        }
        $this->info('All missing transactions inserted');
    }
}
