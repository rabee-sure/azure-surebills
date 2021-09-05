<?php

namespace App\Console\Commands;

use App\Events\BillStatusUpdated;
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

class UpdateTransactionsSettled extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:transactions_settled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Transactions Settled';

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
        $transfers = Transfer::all();
        foreach ($transfers as $key => $transfer) {
            $user = $transfer->user;
            $bills = $transfer->bills;
            $bill_ids = $bills->pluck('id');

            $transactions = Transaction::whereIn('bill_id', $bill_ids)
                    ->where('user_id', $user->id);

            // dd([
            //     'bill_id_count' => $bill_ids->count(), 
            //     'transactions_count' =>  $transactions->count(),
            //     '$user' =>  $user->id,
            // ]);

            $this->updateTranscations($transactions);
        }

    }

    protected function updateTranscations($transactions)
    {
        $transactions->update(['settled' => true]);
    }
}
