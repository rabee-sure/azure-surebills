<?php

namespace App\Listeners;

use App\Events\GenerateReport;
use App\Models\Report;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;
use App\Jobs\SendMerchantOutstandingRepotEmail;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class SendReportFile implements ShouldQueue
{
    use IsMonitored;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function viaQueue()
    {
        return config('queue.working_queues.export_queue');
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\GenerateReport  $event
     * @return void
     */
    public function handle(GenerateReport $event)
    {
        $report = Report::findOrFail($event->report);
        $report_emails = $report->emails;
        $report_filters = json_decode($report->params) ;
        $report_merchants = explode(',', str_replace('"',"",$report_filters->merchants));
        if (in_array("all", $report_merchants))
        {
            $report_merchants = "";
        }
        $report_from = $report_filters->from;
        $report_to = $report_filters->to ?? $report_filters->from;
        $file_name = 'reports/'.$report->name.'/'.$report->name.'_'.$report->id.'.xlsx';
        $whereDateBetween = "";
        $whereDateTo = "";
        if($report_from != '' && $report_to != ''){
            $whereDateBetween = " BETWEEN '".$report_from."' AND '".$report_to."'";
            $whereDateTo = " DATE(created_at) <= '".$report_to."'";
        }
        $whereInMerchants = "";
        if(!empty($report_merchants)){
            $whereInMerchants = "AND users.id IN (".implode(',', $report_merchants).")";
        }
        $results = DB::select("SELECT users.id AS MID, users.business_name_en AS Merchant_Name,
  COALESCE(SUM(CASE WHEN transactions.transaction_source = 'bill' AND transactions.type = 'credit' THEN transactions.amount ELSE 0 END), 0) AS Total_amount_in,
  COALESCE(SUM(CASE WHEN (transactions.transaction_source = 'fees' OR transactions.transaction_source = 'vat') AND transactions.type = 'debit' THEN transactions.amount ELSE 0 END), 0) AS Total_fee_with_vat,
  COALESCE(SUM(CASE WHEN transactions.transaction_source = 'refund' AND transactions.type = 'debit' THEN transactions.amount ELSE 0 END) - SUM(CASE WHEN transactions.transaction_source = 'refund' AND transactions.type = 'credit' THEN transactions.amount ELSE 0 END), 0) AS Total_refund,
  COALESCE(SUM(settlements.transfer_fees), 0) AS Total_transfer_fees,
  COALESCE(SUM(settlements.net_amount), 0) AS Total_net_transfer,
  total_trans.balance AS Outstanding_balance,
  to_date_trans.balance AS Range_balance
FROM users LEFT JOIN transactions ON users.id = transactions.user_id AND transactions.created_at ". $whereDateBetween ."
LEFT JOIN settlements ON users.id = settlements.user_id AND settlements.updated_at " . $whereDateBetween . " AND settlements.status = 'completed'
LEFT JOIN ( SELECT user_id, (SUM( CASE WHEN type = 'credit' THEN amount ELSE 0 END) - SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END)) AS balance FROM
    transactions GROUP BY user_id) AS total_trans ON users.id = total_trans.user_id
LEFT JOIN ( SELECT user_id, ( SUM( CASE WHEN type = 'credit' THEN amount ELSE 0 END ) - SUM( CASE WHEN type = 'debit' THEN amount ELSE 0 END)) AS balance
  FROM transactions WHERE " . $whereDateTo . " GROUP BY user_id) AS to_date_trans ON users.id = to_date_trans.user_id
WHERE users.verified = 1 " . $whereInMerchants . " GROUP BY users.id");
        if(Excel::store(new ReportExport($results), $file_name , 'public')){
            $report->addMedia(storage_path('app/public/'.$file_name))
                ->preservingOriginal()
                ->toMediaCollection('reports_file');
            SendMerchantOutstandingRepotEmail::dispatch($report,$report_emails);
            $report->active = 1;
            $report->save();
        }
    }
}
