<?php

namespace App\Console\Commands;

use App\Events\BillStatusUpdated;
use App\Events\UserCreated;
use App\Jobs\MakeTransactionsForSureBills;
use App\Models\Application;
use App\Models\Bill;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClearBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:bills';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear All Bills';

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
        if(app()->isLocal()){
            DB::table('payment_logs')->truncate();
            DB::table('bill_items')->truncate();
            DB::table('bill_transfer')->truncate();
            DB::table('transactions')->truncate();
            DB::table('settlements')->delete();
            DB::table('bills')->delete();
        }
    }


}
