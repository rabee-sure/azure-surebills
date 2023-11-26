<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MissingTransactionsSummaryExport implements FromArray, WithHeadings
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
            'transaction_id',
            'bill_id',
            'transaction_type',
            'transaction_source',
            'transaction_amount',
            'user_id',
        ];
    }

    public function array(): array
    {
        return $this->results;
    }
}
