<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\Bill;
use App\Events\BillStatusUpdated;
use App\Events\UserCreated;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class SurepayGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'surepay:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate sure easy account';

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
        $email = $this->ask('What is your email?', 'admin@surepay.sa');
        $password = $this->secret('What is the password?');
        $website = $this->ask('What is surepay website?','https://surepay.sa/');

         $user = User::updateOrCreate([
            'email'    => $email,
        ],[
            'business_name_en' => 'surepay',
            'name'     => 'sure easy admin',
            'mobile'   => '500000000',
            'password' => Hash::make($password),
            'able_refund_with_fees' => false,
        ]);
        $user->save();
        event(new UserCreated($user));
        $user->applications()->delete();

        $application = new Application;
        $application->user_id           = $user->id;
        $application->name              = 'surepay';
        $application->secret            = Str::random(20);
        $application->redirect          = $website .'easy/offer/callback';
        $application->webhook_url       = $website .'easy/offer/callback';
        $application->webhook_secret    = Str::random(20);
        $application->save();

        $this->comment('--> ClIENT ID');
        $this->info($application->id);        
        $this->comment('--> SECRET');
        $this->info($application->secret);
        $this->comment('-->WEBHOOK SECRET');
        $this->info($application->webhook_secret);

    }
}
