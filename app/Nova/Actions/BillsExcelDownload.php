<?php

namespace App\Nova\Actions;

use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

class BillsExcelDownload extends DownloadExcel implements WithMapping
{
    /**
     * Get the displayable name of the metric.
     *
     * @return string
     */
    public function name()
    {
        return  __('Download Bills Excel');
    }

    public function headings(): array
    {
        return[
            'ID', 
            'Name', 
            'MID', 
            'Merchant Name',
            'Source',
            'Card Type',
            'Total Paid',
            'VAT Percentage',
            'Total Fees',
            'Total Fees VAT',
            'Total Fees Percentage',
            'Total Fees Fixed',
            'SureBills Fees',
            'SureBills Fees VAT',
            'SureBills Fees Percentage',
            'SureBills Fees Fixed',
            'Status',
            'Refund Amount',
            'Channel Name',
            'Channel Fees',
            'Channel Fees VAT',
            'Channel Fees Percentage',
            'Channel Fees Fixed',
            'Channel Relation',
            'Total Due',
            'Paid At',
        ];
    }

    public function map($bill): array
    {
        return [
            $bill->id,
            $bill->name,
            $bill->user_id,
            $bill->user->business_name_en ?? $bill->user->business_name_ar,
            $bill->source,
            $bill->payment_method_type,
            $bill->total ?? 0,
            $bill->pricing->vat_percentage ?? '',
            $bill->payment_fees ?? 0,
            $bill->payment_fees_vat ?? 0,
            $bill->pricing->fees_percentage ?? '',
            $bill->pricing->fees_fixed?? '',
            $bill->payment_surebills_fees ?? '',
            $bill->payment_surebills_fees_vat,
            $bill->pricing->surebills_fees_percentage ?? '',
            $bill->pricing->surebills_fees_fixed ?? '',
            $bill->status,
            $bill->refund_amount,
            $bill->channel_name,
            $bill->payment_channel_fees,
            $bill->payment_channel_fees_vat,
            $bill->pricing->channel_fees_percentage ?? '',
            $bill->pricing->channel_fees_fixed ?? '',
            $bill->channel_relation,
            $bill->total_due,
            $bill->paid_at,
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
