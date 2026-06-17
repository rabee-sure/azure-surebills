<?php

namespace App\Console\Commands;

use App\Exports\ReportExport;
use App\Events\GenerateReport;
use App\Http\Resources\TransactionExportResource;
use App\Models\Report;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Valuestore\Valuestore;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:excel {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create report excel sheet.';

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
        $report = Report::findOrFail($this->argument('id'));

        $report_emails = $report->emails;

        $file_name = 'reports/'.$report->name.'/'.$report->name.'_'.$report->created_at.'.xlsx';

        $data = DB::table('users')
            ->join('transactions', 'users.id', '=', 'transactions.user_id')
            ->join('settlements', 'users.id', '=', 'settlements.user_id')
            ->select(DB::raw("users.id AS MID, 
            users.business_name_en AS Merchant_Name,
            SUM(CASE WHEN transactions.transaction_source = 'bill' AND transactions.type = 'credit' THEN transactions.amount ELSE 0 END) AS Total_amount_in,
            SUM(CASE WHEN (transactions.transaction_source = 'fees' OR transactions.transaction_source = 'vat') AND transactions.type = 'debit' THEN transactions.amount ELSE 0 END) AS Total_fee_with_vat,
            (SUM(CASE WHEN transactions.transaction_source = 'refund' AND transactions.type = 'credit' THEN transactions.amount ELSE 0 END) - SUM(CASE WHEN transactions.transaction_source = 'refund' AND transactions.type = 'debit' THEN transactions.amount ELSE 0 END)) AS Total_refund,
            SUM(settlements.transfer_fees) AS Total_transfer_fees,
            SUM(settlements.net_amount) AS Total_net_transfer,
            (SUM(CASE WHEN transactions.settled = 0 AND transactions.type = 'credit' THEN transactions.amount ELSE 0 END) - SUM(CASE WHEN transactions.settled = 0 AND transactions.type = 'debit' THEN transactions.amount ELSE 0 END)) AS Outstanding_balance"))
            ->where('users.verified', 1)
            ->groupBy('users.id')
            ->get();

        
        if(Excel::store(new ReportExport($data), $file_name , 'public')){
            
            $report->addMediaFromDisk($file_name, 'public')
                ->preservingOriginal()
                ->toMediaCollection('reports_file');
                
            //fire event transfer file generated
            event(new GenerateReport($report_emails, $report));
        }
    }
}
