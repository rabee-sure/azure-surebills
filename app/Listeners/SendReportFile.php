<?php

namespace App\Listeners;

use App\Events\GenerateReport;
use App\Models\Report;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequestReportMail;
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
        //
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
        $report_to = $report_filters->to;

        $file_name = 'reports/'.$report->name.'/'.$report->name.'_'.$report->id.'.xlsx';

        // $transactionsQuery = DB::table('transactions AS transactions')
        // ->select(DB::raw("(SELECT user_id, amount AS amount, transaction_source AS transaction_source, type as type, settled as settled)"));
        // // **************************************
        // $total_transQuery = DB::table('transactions AS total_trans')
        // ->select(DB::raw("(SELECT user_id, (SUM(CASE WHEN settled = 0 AND type = 'credit' THEN amount ELSE 0 END) - SUM(CASE WHEN settled = 0 AND type = 'debit' THEN amount ELSE 0 END)) AS balance)"));
        // if($report_from != ''){
        //     $transactionsQuery->whereDate('transactions.created_at', '>=', $report_from);
        // }
        // if($report_to != ''){
        //     $transactionsQuery->whereDate('transactions.created_at', '<=', $report_to);
        // }
        // $transactionsQuery->groupBy('users.id');
        // // ********************************************
        // $to_date_transQuery = DB::table('transactions AS to_date_trans')
        // ->select(DB::raw("(SELECT user_id, (SUM(CASE WHEN settled = 0 AND type = 'credit' THEN amount ELSE 0 END) - SUM(CASE WHEN settled = 0 AND type = 'debit' THEN amount ELSE 0 END)) AS balance)"));
        // if($report_to != ''){
        //     $to_date_transQuery->whereDate('transactions.created_at', '<=', $report_to);
        // }
        // $to_date_transQuery->groupBy('users.id');
        // // ************************************************
        // $settlementsQuery = DB::table('settlements AS settlements')
        // ->select(DB::raw("(SELECT user_id, transfer_fees AS transfer_fees, net_amount AS net_amount)"));
        // if($report_from != ''){
        //     $settlementsQuery->whereDate('settlements.created_at', '>=', $report_from);
        // }

        // if($report_to != ''){
        //     $settlementsQuery->whereDate('settlements.created_at', '<=', $report_to);
        // }

        // $query = DB::table('users')
        //     ->leftJoin($transactionsQuery, 'users.id', '=', 'transactions.user_id')
        //     ->leftJoin($total_transQuery, 'users.id', '=', 'total_trans.user_id')
        //     ->leftJoin($to_date_transQuery, 'users.id', '=', 'to_date_trans.user_id')
        //     ->leftJoin($settlementsQuery, 'users.id', '=', 'settlements.user_id')
        //     ->select(DB::raw("users.id AS MID, 
        //     users.business_name_en AS Merchant_Name,
        //     SUM(CASE WHEN transactions.transaction_source = 'bill' AND transactions.type = 'credit' THEN transactions.amount ELSE 0 END) AS Total_amount_in, 
        //     SUM(CASE WHEN (transactions.transaction_source = 'fees' OR transactions.transaction_source = 'vat') AND transactions.type = 'debit' THEN transactions.amount ELSE 0 END) AS Total_fee_with_vat, 
        //     (SUM(CASE WHEN transactions.transaction_source = 'refund' AND transactions.type = 'debit' THEN transactions.amount ELSE 0 END) - SUM(CASE WHEN transactions.transaction_source = 'refund' AND transactions.type = 'credit' THEN transactions.amount ELSE 0 END)) AS Total_refund,
        //     SUM(settlements.transfer_fees) AS Total_transfer_fees, 
        //     SUM(settlements.net_amount) AS Total_net_transfer,
        //     total_trans.balance AS Outstanding_balance,
        //     to_date_trans.balance AS Range_balance"))
        //     ->where('users.verified', 1);

        // if(!empty($report_merchants)){
        //     $query->whereIn('transactions.user_id', $report_merchants);
        // }
            
        // $query->groupBy('users.id');
            
        // $data = $query->get();

        $whereDateBetween = "";
        $whereDateTo = "";

        if($report_from != '' && $report_to != ''){
            $whereDateBetween = "WHERE DATE(created_at) BETWEEN '".$report_from."' AND '".$report_to."'";
            $whereDateTo = "WHERE DATE(created_at) <= '".$report_to."'";
        }

        $whereInMerchants = "";

        if(!empty($report_merchants)){
            $whereInMerchants = "AND users.id IN (".implode(',', $report_merchants).")";
        }

        $results = DB::select("select 
            users.id AS MID, 
            users.business_name_en AS Merchant_Name,
            Total_amount_in_transactions.Total_amount AS Total_amount_in, 
            Total_fee_with_vat_transactions.Total_amount AS Total_fee_with_vat, 
            Total_refund_transactions.Total_amount AS Total_refund,
            settlements.Total_transfer_fees AS Total_transfer_fees, 
            settlements.Total_net_transfer AS Total_net_transfer,
            total_trans.balance AS Outstanding_balance,
            to_date_trans.balance AS Range_balance
        
        FROM (SELECT id, business_name_en, verified FROM users) AS users 
        
        LEFT JOIN (SELECT user_id, SUM(CASE WHEN transactions.transaction_source = 'bill' AND transactions.type = 'credit' THEN transactions.amount ELSE 0 END) AS Total_amount FROM `transactions` ".$whereDateBetween." GROUP BY user_id) AS Total_amount_in_transactions on `users`.`id` = Total_amount_in_transactions.user_id 
        
        LEFT JOIN (SELECT user_id, SUM(CASE WHEN (transactions.transaction_source = 'fees' OR transactions.transaction_source = 'vat') AND transactions.type = 'debit' THEN transactions.amount ELSE 0 END) AS Total_amount FROM `transactions` ".$whereDateBetween." GROUP BY user_id) AS Total_fee_with_vat_transactions on `users`.`id` = Total_fee_with_vat_transactions.user_id 
        
        LEFT JOIN (SELECT user_id, (SUM(CASE WHEN transactions.transaction_source = 'refund' AND transactions.type = 'debit' THEN transactions.amount ELSE 0 END) - SUM(CASE WHEN transactions.transaction_source = 'refund' AND transactions.type = 'credit' THEN transactions.amount ELSE 0 END)) AS Total_amount FROM `transactions` ".$whereDateBetween." GROUP BY user_id) AS Total_refund_transactions on `users`.`id` = Total_refund_transactions.user_id 
        
        LEFT JOIN (SELECT user_id, (SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) - SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END)) AS balance FROM `transactions` GROUP BY user_id) AS total_trans on `users`.`id` = total_trans.user_id 
        
        LEFT JOIN (SELECT user_id, (SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) - SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END)) AS balance FROM `transactions` ".$whereDateTo." GROUP BY user_id) AS to_date_trans on `users`.`id` = to_date_trans.user_id
        
        LEFT JOIN (SELECT user_id, SUM(transfer_fees) AS Total_transfer_fees, SUM(settlements.net_amount) AS Total_net_transfer FROM `settlements` ".$whereDateBetween." AND settlements.status like 'completed' GROUP BY user_id) AS settlements on `users`.`id` = `settlements`.`user_id`
        
        WHERE `users`.`verified` = 1
        ".$whereInMerchants."
        GROUP BY  `users`.`id`");

        if(Excel::store(new ReportExport($results), $file_name , 'public')){
            
            $report->addMedia(storage_path('app/public/'.$file_name))
                ->preservingOriginal()
                ->toMediaCollection('reports_file');

            $emails = explode(",", $report_emails);
            if(count($emails)){
                foreach ($emails as $email) {
                    $message = (new RequestReportMail($report))->onQueue(env('EMAILS_QUEUE'));
                    Mail::to($email)->queue($message);
                }
            }

            $report->active = 1;
            $report->save();
        }
    }
}
