<?php

namespace App\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MerchantsBalanceExport implements FromArray, WithHeadings
{
    use Exportable;

    protected $results;
    
    public function __construct(array $results)
    {
        $this->results = $results;
    }

    public function headings(): array
    {
        return [
            'Merchant',
            'Total credit',
            'Total debit',
            'Balance',
            'Count transfers',
            'Total transfers amount',
            'Count completed transfers',
            'Total completed transfers amount',
            'Count pending transfers',
            'Total pending transfers amount',
            'Count unsettled transactions',
            'Total unsettled transactions amount'
        ];
    }

    public function array(): array
    {
        return $this->results;
    }
}
