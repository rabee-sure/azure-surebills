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
        // completed
        $transfers = Transfer::all();
        foreach ($transfers as $key => $transfer) {
            $this->info('transfer '. $transfer->id. ' status '.  $transfer->status);
            $user = $transfer->user;
            $bills = $transfer->bills;
            $bill_ids = $bills->pluck('id');


            $transactions_id = Transaction::whereIn('bill_id', $bill_ids)
                ->where('user_id', $user->id)->pluck('id')->toArray();
            $transfer->transactions()->sync($transactions_id);


            if($transfer->status == 'completed'){
                Transaction::whereIn('bill_id', $bill_ids)
                    ->where('user_id', $user->id)
                    ->update(['settled' => true]);
            }elseif($transfer->status == 'pending'){
                Transaction::whereIn('bill_id', $bill_ids)
                    ->where('user_id', $user->id)
                    ->update(['pending_settled' => true]);
            }
        }

    }


       
}
