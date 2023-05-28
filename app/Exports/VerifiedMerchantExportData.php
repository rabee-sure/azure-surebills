<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class VerifiedMerchantExportData implements FromQuery, WithHeadings, WithMapping, WithEvents
{
    use Exportable;

    public function headings(): array
    {
        return ['Name', 'Email', 'Mobile'];
    }

    public function map($merchant): array
    {
        return [
            $merchant->name,
            $merchant->email,
            $merchant->mobile,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return User::query()->where([['verified', true], ['store_main_user_id', null], ['source', '<>', 'pos']]);
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                foreach (range('A', $sheet->getHighestColumn()) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                 }
            }
        ];
    }
}
