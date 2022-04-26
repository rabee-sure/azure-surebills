<?php

namespace App\Listeners;

use App\Events\GenerateBillReport;
use App\Exports\ReportBillExport;
use App\Models\Report;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequestReportMail;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class SendBillReportFile implements ShouldQueue
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
    public function handle(GenerateBillReport $event)
    {
        $report = Report::findOrFail($event->report);

        $report_emails = $report->emails;

        $report_filters = json_decode($report->params, true) ;

        $file_name = 'reports/'.$report->name.'/'.$report->name.'_'.$report->id.'.xlsx';

        $whereCondition = "where (paid_at between '".$report_filters['paid_from']."' and '".$report_filters['paid_to']."' and bills.status in ('paid', 'refunded'))";

        if($report_filters['merchants'] == '' && $report_filters['channels'] != '')
        {
            $whereCondition .= " and channels.id in (".str_replace('"','',$report_filters['channels']).")";
        }
        else if($report_filters['merchants'] != '' && $report_filters['channels'] == '')
        {
            $whereCondition .= " and bills.user_id in (".str_replace('"','',$report_filters['merchants']).")";
        }
        else if($report_filters['merchants'] != '' && $report_filters['channels'] != '')
        {
            $whereCondition .= " and (bills.user_id in (".str_replace('"','',$report_filters['merchants']).") or channels.id in (".str_replace('"','',$report_filters['channels'])."))";
        }

        $results = DB::select("select
            bills.user_id as MID,
            users.business_name_en as Merchant_Name,
            bills.id as Payment_gateway_ID,
            bills.paid_at,
            bills.status,
            channels.name as Channel_Name,
            bills.total,
            payment_logs.brand as Card_type,
            JSON_EXTRACT(bills.pricing, '$.vat_percentage') as vat_percentage,
            bills.payment_fees as Total_Fees,
            bills.payment_fees_vat as Total_Fees_VAT,
            JSON_EXTRACT(bills.pricing, '$.fees_fixed') as total_fees_fixed,
            JSON_EXTRACT(bills.pricing, '$.fees_percentage') as total_fees_percentage,
            bills.payment_channel_fees as Channel_Fees,
            bills.payment_channel_fees_vat as Channel_Fees_VAT,
            JSON_EXTRACT(bills.pricing, '$.channel_fees_fixed') as channel_fees_fixed,
            JSON_EXTRACT(bills.pricing, '$.channel_fees_percentage') as channel_fees_percentage,
            bills.payment_surebills_fees as Surebills_Fees,
            bills.payment_surebills_fees_vat as Surebills_Fees_VAT,
            JSON_EXTRACT(bills.pricing, '$.surebills_fees_fixed') as surebills_fees_fixed,
            JSON_EXTRACT(bills.pricing, '$.surebills_fees_percentage') as surebills_fees_percentage,
            bills.refund_amount,
            transaction_transfer.transfer_id as Transfer_id
        from bills
            left join users on users.id = bills.user_id
            left join payment_logs on bills.id = payment_logs.bill_id and payment_logs.status = 1
            left join applications on bills.application_id = applications.id
            left join channels on applications.channel_id = channels.id
            left join transactions on bills.id = transactions.bill_id
            left join transaction_transfer on transactions.id = transaction_transfer.transaction_id
        $whereCondition
        group by bills.id
        order by paid_at;");

        if(Excel::store(new ReportBillExport($results), $file_name , 'public')){

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
