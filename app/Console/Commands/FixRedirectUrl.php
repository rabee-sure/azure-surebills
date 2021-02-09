<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FixRedirectUrl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:redirect_url';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fix redirect_url';

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
        $apps = Application::all();
        foreach ($apps as $app) {
            $redirect_array = explode("//", $app->redirect);
            $webhook_url_array = explode("//", $app->webhook_url);

            if(count($redirect_array) == 3){   
                $app->redirect = $redirect_array[0].'//'.$redirect_array[1].'/'.$redirect_array[2];
                $app->save();
                $this->info("fixx app id: {$app->id} redirect url!");
            }
            
            if(count($webhook_url_array) == 3){   
                $app->webhook_url = $webhook_url_array[0].'//'.$webhook_url_array[1].'/'.$webhook_url_array[2];
                $app->save();
                $this->info("fixx app id: {$app->id} redirect url!");
            }
        }
    }
}
