<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\SMSService;
use Illuminate\Console\Command;

class SendSMSCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send_code {--mobile=} {--code=} {--user_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this command for test send sms';

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
        $user_id = $this->option('user_id');
        
        if($user_id){
            $user = User::find($user_id);
            
            $user->sendMobileCode();
        }
        else{
            $mobile = (int) $this->option('mobile');
            $message = $this->option('code');
            $smsService = new SMSService();

            $response = $smsService->sendSMS($mobile, $message);
            if ($response) {
                dd($response);
            }
            // if (app()->environment('production')) {
            // }
        }
    }
}
