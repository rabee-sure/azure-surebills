<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;

class BillsDataExport implements FromQuery, WithHeadings, ShouldQueue
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
            'Bill Number',
            'Bill Status',
            'Bill Created at',
            'Payment Method',
            'Customer Name',
            'Customer Mobile',
            'Customer Email',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $billsQuery = DB::table('bills')
        ->select('number', 'status', 'created_at', 'payment_method', 'customer_name', 'customer_mobile', 'customer_email');

        if($this->filter['status'] != null){
            $billsQuery = $billsQuery->whereIn('status', $this->filter['status']);
        }


        if($this->filter['application_id'] == 1){
            $billsQuery = $billsQuery->whereNotNull('application_id');
        }elseif($this->filter['application_id'] == 2){
            $billsQuery = $billsQuery->whereNull('application_id');
        }

        if($this->filter['created_at'] != null){
            $from = Carbon::parse($this->filter['created_at'][0])->startOfDay();
            $to = Carbon::parse($this->filter['created_at'][1])->endOfDay();

            $billsQuery = $billsQuery->whereBetween('created_at', [$from, $to]);
        }

        if($this->filter['paid_at'] != null){
            $from = Carbon::parse($this->filter['paid_at'][0])->startOfDay();
            $to = Carbon::parse($this->filter['paid_at'][1])->endOfDay();

            $billsQuery = $billsQuery->whereBetween('paid_at', [$from, $to]);
        }

        if($this->filter['refunded_at'] != null){
            $from = Carbon::parse($this->filter['refunded_at'][0])->startOfDay();
            $to = Carbon::parse($this->filter['refunded_at'][1])->endOfDay();

            $billsQuery = $billsQuery->whereBetween('refunded_at', [$from, $to]);
        }

        if($this->filter['user_id'] != null){
            $billsQuery = $billsQuery->where('user_id', $this->filter['user_id']);
        }
        
        return $billsQuery;
    }
}
