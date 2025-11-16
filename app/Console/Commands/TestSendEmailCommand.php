<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestSendEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:sendToTest {--email=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test send email';

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
     * @return mixed
     */
    public function handle()
    {
        try{
            Mail::send([], [], function ($message){
                $message->to($this->option('email'))
                    ->subject('Test send email from SureBills') // here comes what you want
                    ->setBody('Hi, welcome user!'); // assuming text/plain
            });
            $this->info('done');
        } catch(Exception $e){
            dd($e->getMessage());
        }
    }
}
