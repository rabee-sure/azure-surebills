<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;

class BillCustomerDataExport implements FromQuery, WithHeadings
{
    use Exportable;

    public function headings(): array
    {
        return [
            'Bill Number',
            'Bill Status',
            'Bill Created at',
            'Payment Method',
            'Customer Name',
            'Customer Mobile',
            'Customer Email',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $query = DB::table('bills')
        ->select('number', 'status', 'created_at', 'payment_method', 'customer_name', 'customer_mobile', 'customer_email')
        ->where('user_id', 1654)
        ->whereIn('status', ['paid','paid_cash','paid_bank_transfer', 'paid_machine'])
        ->orderByDesc('created_at');
        return $query;
    }
}
