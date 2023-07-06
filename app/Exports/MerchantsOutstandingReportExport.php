<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MerchantsOutstandingReportExport implements FromArray, WithHeadings
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
            'MID',
            'Merchant Name',
            'Business Name',
            'Total Amounts',
            'Bills Count',
        ];
    }

    public function array(): array
    {
        return $this->results;
    }
}
