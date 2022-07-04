<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Exportable;

use App\Models\User;

class PaymentRecordExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public $request;
    
    public function __construct($request)
    {
        $this->request = $request;
    }

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
        $user = Auth::user()->store_main_user_id ? User::find(Auth::user()->store_main_user_id) : Auth::user();

        $query = $user->paymentRecordQuery($this->request);

        return $query;
    }

    public function map($payments): array
    {
        return [
            $payments->created_at,
            __('reports.'.$payments->type),
            $payments->reference,
            $payments->payment_way ? __('reports.'.$payments->payment_way) : null,
            $payments->source ? __('reports.'.$payments->source) : null,
            fact_number(round($payments->amount, 2)),
        ];
    }
}
