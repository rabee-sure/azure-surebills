<?php

namespace App\Nova\Actions;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

class MerchantsExcelDownload extends DownloadExcel implements WithMapping
{
    /**
     * Get the displayable name of the metric.
     *
     * @return string
     */
    public function name()
    {
        return  __('Download Merchants Excel');
    }

    public function headings(): array
    {
        return['ID', __('Merchant Name'), __('Phone'), __('Email'), __('Business Name'), __('Commercial Record'), __('City'), __('Address'), __('Total transactions amount per Year')];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->mobile,
            $user->email,
            $user->business_name_en,
            $user->vat_registration_number,
            $user->city,
            $user->business_address,
            $user->Total_amounts,
        ];
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [];
    }
}
