<?php

namespace App\Exports;

use App\Models\Bill;
use App\Models\RefundedBill;
use App\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransferBillsExportData implements FromQuery, WithHeadings, WithMapping, ShouldQueue
{
    use Exportable;

    public $transfer;

    public function __construct($transfer)
    {
        $this->transfer = $transfer;
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
        $bills_ids = $this->transfer->transactions->unique('bill_id')->pluck('bill_id')->toArray();

        $bills = Bill::whereIn('id', $bills_ids)
        ->select('id', DB::raw("(CASE WHEN debit_note_bill_id IS NULL THEN number ELSE CONCAT('DN', number) END) AS number"), 'customer_name', 'sub_total', 'vat', 'discount', 'status', DB::raw("'null' as method"),'created_at', DB::raw("'bills' as model"), 'debit_note_bill_id');

        return $bills;
    }

}