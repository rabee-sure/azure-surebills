<?php

namespace App\Console\Commands;

use App\Events\BillStatusUpdated;
use App\Events\UserCreated;
use App\Jobs\MakeTransactionsForSureBills;
use App\Models\Application;
use App\Models\Bank;
use App\Models\Bill;
use App\Models\Transaction;
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
            'able_refund_with_fees' => false,
            'business_address' => 'surebills address',
            'business_mobile' => '00000000000',
            'business_name_ar'=> 'surebills',
            'organization_name'=> 'surebills',
            'beneficiary_name'=> 'surebills',
            'bank_id'=> Bank::inRandomOrder()->first()->id,
            'iban_number'=> 'SA5480000362608011111129',
        ]);
        $user->save();
        $this->info("surbills email: {$user->email}");
        $this->info("surbills password: {$user->password}");
        event(new UserCreated($user));

        $this->info('surebills created successfuly');

        if ($this->confirm('Do you wish to continue make old transaction ?')) {
            Transaction::where('user_id', $user->id)->delete();
            Bill::paid()->chunk('200', function ($bills){
                foreach($bills as $bill){
                    $this->info("calculate trnasaction from bill: {$bill->id}");
                    //make Transactions For SureBills
                    MakeTransactionsForSureBills::dispatch($bill, null);
                }
            });
        }
    }
}
