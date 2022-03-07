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

        $query = DB::table('users')
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
            ->where('users.verified', 1);

        if(!empty($report_merchants)){
            $query->whereIn('transactions.user_id', $report_merchants);
        }

        if($report_from != ''){
            $query->whereDate('transactions.created_at', '>=', $report_from);
        }

        if($report_to != ''){
            $query->whereDate('transactions.created_at', '<', $report_to);
        }
            
        $query->groupBy('users.id');
            
        $data = $query->get();

        if(Excel::store(new ReportExport($data), $file_name , 'public')){
            
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
