<?php

namespace App\Console\Commands;

use App\Models\AutoTransfer;
use App\Models\Transaction;
use Illuminate\Console\Command;

class CountTransactionsOfAutoTransfer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:transactions_count {auto_transfer_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Count transactions of transfers';

    protected $CHANNEL_SOURCES = [
        'channel_extra_amount',
        'channel_extra_amount_vat',
        'channel_extra_amount_fees',
        'channel_fees',
        'channel_vat',
    ];

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
        $autoTransfer = AutoTransfer::find($this->argument('auto_transfer_id'));
        $transfer_ids = $autoTransfer->tranfer_ids;
        
        $transactions = Transaction::whereHas('transfers', function($q) use($transfer_ids){
            $q->whereIn('transfer_id', $transfer_ids)
                ->whereNotIn('transaction_source', $this->CHANNEL_SOURCES)
                ->where('description', 'not like', "%Channel:%");
        })->with('bill.application.channel')->count();

        $this->info("Transactions count: {$transactions}");
    }
}
