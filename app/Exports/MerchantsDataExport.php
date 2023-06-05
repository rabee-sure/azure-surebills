<?php

namespace App\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;

class MerchantsDataExport implements FromQuery, WithHeadings, WithMapping, ShouldQueue
{
    use Exportable;

    protected $filter;

    public function __construct($filter)
    {
        $this->filter = $filter;
    }

    public function headings(): array
    {
        return [
            'ID', 
            __('Merchant Name'), 
            __('Phone'), 
            __('Email'), 
            __('Business Name'), 
            __('Type of license'), 
            __('City'), 
            __('Address'), 
            __('Amount of transactions')
        ];
    }

    public function map($user): array
    {
       return [
            $user->id,
            $user->name,
            $user->mobile,
            $user->email,
            $user->business_name_en,
            __($user->license_type),
            $user->business_address,
            $user->business_address_details,
            $user->total_amounts,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $reportQuery = DB::table('users')
        ->join('transactions', 'users.id', '=', 'transactions.user_id')
        ->select(
            'users.id as id', 
            'users.name as name', 
            'users.mobile as mobile', 
            'users.email as email', 
            'users.business_name_en as business_name_en', 
            'users.license_type as license_type', 
            'users.business_address as business_address', 
            'users.business_address_details as business_address_details', 
            'transactions.user_id', 
            DB::raw("SUM(transactions.amount) AS total_amounts")
        )
        ->where([['verified', true], ['store_main_user_id', null]])
        ->where('transactions.type', 'credit');

        if($this->filter['created_at'] != null){
            $reportQuery = $reportQuery->whereYear('transactions.created_at', $this->filter['created_at']);
        }

        $reportQuery = $reportQuery->groupBy('transactions.user_id');
        // dd($reportQuery->toSql());
        return $reportQuery;
    }

}
