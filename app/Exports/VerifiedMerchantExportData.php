<?php

namespace App\Exports;

use App\Models\Bill;
use App\Models\RefundedBill;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Events\AfterSheet;

class VerifiedMerchantExportData implements FromQuery, WithHeadings, WithMapping, ShouldQueue, WithEvents
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
