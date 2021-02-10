<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\Bill;
use App\Events\BillStatusUpdated;
use App\Events\UserCreated;
use App\Jobs\MakeTransactionsForSureBills;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateSureBillsAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'surebills:account';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Sure Bills Account';

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
         $user = User::updateOrCreate([
            'email'    => 'surebills@sura.com.sa',
        ],[
            'business_name_en' => 'surebills',
            'name'     => 'surebills admin',
            'mobile'   => '500000000',
            'password' => Hash::make('123456789Aa'),
        ]);
        $user->save();
        event(new UserCreated($user));

        $this->info('surebills created successfuly');

        if ($this->confirm('Do you wish to continue make old transaction ?')) {

            foreach(Bill::paid()->get() as $bill){
                //make Transactions For SureBills
                MakeTransactionsForSureBills::dispatch($bill, null);
            }
        }
    }
}
