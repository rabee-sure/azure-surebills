<?php

namespace App\Console\Commands;

use App\Bill;
use App\Events\BillStatusUpdated;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SurepayGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Surepay:generate';

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
        $user                  = new User;
        $user->business_name   = 'surepay';
        $user->name            = 'sure easy admin';
        $user->email           = 'admin@sure';
        $user->mobile          = $request->mobile;
        $user->fandaqah_user   = true;
        $user->password        = $request->email . $request->name;
        $user->save();
        event(new UserCreated($user));

        $application = new Application;
        $application->user_id           = $user->id;
        $application->name              = 'FANDAQAH';
        $application->secret            = Str::random(20);
        $application->redirect          = $request->redirect_url;
        $application->webhook_url       = $request->webhook_url;
        $application->webhook_secret    = Str::random(20);
        $application->save();

        return [
            'client_id'      => $application->id,
            'secret'         => $application->secret,
            'webhook_secret' => $application->webhook_secret
        ];
    }
}
