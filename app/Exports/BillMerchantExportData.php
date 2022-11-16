<?php

namespace App\Exports;

use App\Models\Bill;
use App\Models\RefundedBill;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;

class BillMerchantExportData implements FromQuery, WithHeadings, WithMapping, ShouldQueue
{
    use Exportable;

    protected $filter;

    public function __construct($filter)
    {
        $this->filter = $filter;
    }

    public function headings(): array
    {
        return [
            'Bill name',
            'Value',
            'Creation Date',
            'Status',
        ];
    }

    public function map($bill): array
    {
        $bill_name = '';
        if($bill->model == 'bills' && $bill->debit_note_bill_id == null){
            $bill_name .= 'Bill';
        } 
        $bill_name .= $bill->number;
        if($bill->customer_name != null){
            $bill_name .= '-';  
        } 
        $bill_name .= $bill->customer_name;

        $bill_value = $bill->sub_total + $bill->vat - $bill->discount .' SAR';

        return [
            $bill_name,
            $bill_value,
            $bill->created_at,
            $bill->status,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $user_id = $this->filter['user_id'];
        $statuses = $this->filter['statuses'];
        $date_start = $this->filter['date_start'];
        $date_to = $this->filter['date_to'];

        $bills = Bill::userId($user_id)
            ->when($statuses, function ($q) use ($statuses) {
                $q->whereIn('status', $statuses);
            })
            ->when($date_start, function ($q) use ($date_start, $date_to) {
                $q->whereDate('created_at', '>=', Carbon::parse($date_start))
                    ->whereDate('created_at', '<=', Carbon::parse($date_to));
            })
            ->select('id', DB::raw("(CASE WHEN debit_note_bill_id IS NULL THEN number ELSE CONCAT('DN', number) END) AS number"), 'customer_name', 'sub_total', 'vat', 'discount', 'status', DB::raw("'null' as method"),'created_at', DB::raw("'bills' as model"), 'debit_note_bill_id');

        $refundedBills = RefundedBill::userId($user_id)
        ->when($statuses, function ($q) use ($statuses) {
            $q->whereIn('status', $statuses);
        })
        ->when($date_start, function ($q) use ($date_start, $date_to) {
            $q->whereDate('created_at', '>=', Carbon::parse($date_start))
                ->whereDate('created_at', '<=', Carbon::parse($date_to));
        })
        ->select('id', DB::raw("CONCAT('CN', number) as number"), 'customer_name', 'amount as sub_total', DB::raw("'0' as vat"), DB::raw("'0' as discount"), 'status', 'method', 'created_at', DB::raw("'refundedbills' as model"), DB::raw("'' as debit_note_bill_id"));

        $mergedBills = $bills->union($refundedBills)->orderBy('created_at', 'desc');
        
        return $mergedBills;
    }

}
