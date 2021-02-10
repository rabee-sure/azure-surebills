<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\Settings;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SeedSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed Settings';

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
        $users = User::doesntHave('settings')->get();
        foreach ($users as $user) {
            $this->info(" user id: {$user->id}!");

            $settings = Settings::create([
                'add_tax' => false,
                'tax_value' => 0,
                'default_lang' => 'en',
                'active_lang' => 'en',
                'create_send_sms' => false,
                'create_send_email' => false,
                'paid_send_sms' => false,
                'paid_send_email' => false,
                'user_id' => $user->id,
            ]);
  
        }
    }
}
