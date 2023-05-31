<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\Transfer;

class CreateMissingTransferTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:create_transfer_transaction {transfer_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'command for create missing transfer transaction and update balance after transaction creation';

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
        $transfer_id = $this->argument('transfer_id');

        $transfer = Transfer::findOrFail($transfer_id);

        $bankCode   = $transfer->user->bank ? $transfer->user->bank->code : '-';
        $bankNumber = substr($transfer->user->iban_number, -4);

        $transaction = new Transaction;
        $transaction->user_id     = $transfer->user_id;
        $transaction->type        = 'debit';
        $transaction->amount      = $transfer->amount;
        $transaction->reference   = $transfer->id;
        $transaction->description = 'Transfer - ' . $bankCode . ' XXXX' . $bankNumber;
        $transaction->transaction_source = 'transfer';
        $transaction->created_at = $transfer->updated_at;
        $transaction->updated_at = $transfer->updated_at;
        $transaction->save();
        
        $this->info("transaction ID = {$transaction->id} created"); 
        
        $created_at = date('Y-m-d H:i:s', strtotime($transaction->created_at));
        $amount = $transaction->amount;

        $transactions_after_creation = Transaction::where('created_at', '>', $created_at)->where('user_id', $transfer->user_id)->get();

        
        foreach($transactions_after_creation as $tran){
            $tran->balance -= $amount;
            $tran->save();
            $this->info("transaction ID = {$tran->id} balance updated");
        }
    }
}