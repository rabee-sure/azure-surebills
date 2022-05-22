<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Exports\BillCustomerDataExport;
use Carbon\Carbon;

class ExportMerchantBillsReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:export-bill-cutomer-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export report for cutomer to display bills and its custmer data';

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
        $file_name = 'bills'.Carbon::now()->toDateString().'.xlsx';
        return (new BillCustomerDataExport())->store($file_name, 'local');
    }
}
