<?php

namespace App\Console\Commands;

use App\Mail\MerchantBillsExportedExcelMail;
use App\Mail\MerchantBillsExportedExcelMailWithoutQueue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEmailTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:sendTo {email} {--file=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send test email for specefic email';

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
        //Send without Queue
        $path = (string) ($this->option('file') ?: 'merchant-bills/bills_test.xlsx');
        $message = (new MerchantBillsExportedExcelMailWithoutQueue($path));
        Mail::to($this->argument('email'))->send($message);

        //Send With Queue
        $message = (new MerchantBillsExportedExcelMail($path));
        Mail::to($this->argument('email'))->send($message);
        
        $this->info('emails sent succefully');

        return 0;
    }
}
