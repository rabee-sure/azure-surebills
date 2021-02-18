<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteUncompletedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:uncompleted';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Uncompleted Users';

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
        $users = User::where('verified', false)
            ->whereNull('business_address')
            ->orWhereNull('business_mobile')
            ->orWhereNull('bank_id')
            ->orWhereNull('iban_number')
            ->orWhereNull('beneficiary_name')
            ->whereDate('created_at','<=', Carbon::now()->subDays(10))
            ->get();

        $bar = $this->output->createProgressBar(count($users));

        $bar->start();

        foreach ($users as $user) {
            $user->applications()->delete();
            $user->channelsApplications()->delete();
            foreach ($user->bills as $key => $bill) {
                $bill->delete();
            }
            
            $user->channels()->delete();
            $user->settings()->delete();
            $user->customers()->delete();
            $user->delete();

            $bar->advance();
        }

        $bar->finish();

    }
}
