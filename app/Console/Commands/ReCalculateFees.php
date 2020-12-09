<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class ReCalculateFees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recalculate:fees {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ReCalculate SureBills fees for all bills that is not settled yet.';

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
        $user = User::findOrFail($this->argument('user_id'));

        foreach ($user->bills()->paid()->notSettled()->get() as $bill) {
            $bill->reCalculateFees();
        }

        return true;
    }
}
