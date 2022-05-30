<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Exportable;

class PaymentRecordExport implements FromQuery, WithHeadings
{
    use Exportable;

    public function headings(): array
    {
        return [
            __('Date'),
            __('Transaction Type'),
            __('Reference'),
            __('Payment Method'),
            __('Source'),
            __('Amount'),
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $user_id = Auth::user()->store_main_user_id ?? Auth::user()->id;

        $query = DB::table('transactions')
        ->join('bills', 'transactions.bill_id', '=', 'bills.id')
        ->whereIn('transactions.transaction_source', ['bill', 'refund'])
        ->where('transactions.user_id', $user_id)
        ->select('transactions.created_at', 'transactions.type', 'transactions.reference', 'bills.payment_way', 'bills.source', 'transactions.amount')
        ->orderByDesc('transactions.created_at');;
        return $query;
    }
}
