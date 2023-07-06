<?php

namespace App\Console\Commands;

use App\Exports\MerchantsOutstandingReportExport;
use App\Exports\ReportExport;
use App\Jobs\SendMerchantOutstandingRepotEmail;
use App\Mail\MerchantsBalancesReportMail;
use App\Models\Report;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class GenerateMerchantOutstandingReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:merchant_outstanding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for generate merchant outstanding report for big data';

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
        ini_set('memory_limit','4096M');

        $results = DB::table('users')
        ->leftJoin('bills', 'bills.user_id', '=', 'users.id')
        ->select(
            "users.id AS MID",
            "users.name AS Merchant_Name",
            "users.business_name_en AS Business_Name",
            DB::raw('SUM(bills.total) AS Totals')
        )
        ->where('users.verified', 1)
        ->groupBy('users.id')
        ->get()->toArray();
        $this->info(count($results));

        if(!empty($results)){
            $file_name = 'merchants/outstanding_report.xlsx';
            if(Excel::store(new MerchantsOutstandingReportExport($results), $file_name , 'public')){
                $this->info('Report Generated');
            }
        }
    }
}
