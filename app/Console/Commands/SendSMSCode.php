<?php

namespace App\Console\Commands;

use App\Models\User;
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
            if (app()->environment('production')) {
                $mobile = $this->option('mobile');
                $message = $this->option('code');

                $data = ["Tagname" => "SURE-Pay", "RecepientNumber" => "0" . $mobile, "Message" => $message, "Username" => config('yamamah.username'), "Password" => config('yamamah.password')];
                $payload = json_encode($data);
                $ch = curl_init('https://api.yamamah.com/SendSMS');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($payload)));
                $result = curl_exec($ch);
                curl_close($ch);
                $response = json_decode($result, true);

                $this->info($result);
            }
        }
    }
}
