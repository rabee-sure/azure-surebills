<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;

class BillsDataExport implements FromQuery, WithHeadings, WithMapping, ShouldQueue
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
        $bill_pricing = [];
        if($bill->pricing){
            $bill_pricing = json_decode($bill->pricing, true);
        }

        return [
            $bill->id,
            $bill->number.'-'.$bill->customer_name,
            $bill->user_id,
            $bill->user_business_name_en ?? $bill->user_business_name_ar,
            $bill->source,
            self::getPaymentMethodDetails($bill),
            $bill->total,
            $bill_pricing['vat_percentage'] ?? '',
            $bill->payment_fees,
            round($bill->payment_fees_vat, 2),
            $bill_pricing['fees_percentage'] ?? '',
            $bill_pricing['fees_fixed'] ?? '',
            round($bill->payment_surebills_fees, 2),
            round($bill->payment_surebills_fees_vat, 2),
            $bill_pricing['surebills_fees_percentage'] ?? '',
            $bill_pricing['surebills_fees_fixed'] ?? '',
            $bill->status,
            $bill->refund_amount,
            $bill->channel_name,
            round($bill->payment_channel_fees, 2),
            round($bill->payment_channel_fees_vat, 2),
            $bill_pricing['channel_fees_percentage'] ?? '',
            $bill_pricing['channel_fees_fixed'] ?? '',
            ($bill->channel_user_id && $bill->channel_user_id != $bill->user_id) ? 'Channel' : 'Owner',
            ($bill->channel_user_id && $bill->channel_user_id != $bill->user_id) ? round($bill->payment_channel_fees + $bill->payment_channel_fees_vat, 2) : round($bill->total - $bill->payment_fees - $bill->payment_fees_vat, 2),
            ($bill->paid_at) ? $bill->paid_at : null
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $billssQuery = DB::table('bills')
        ->join('users', 'bills.user_id', '=', 'users.id')
        ->leftJoin('applications', 'bills.application_id', '=', 'applications.id')
        ->leftjoin('channels', 'applications.channel_id', '=', 'channels.id')
        ->leftJoin(DB::raw('(SELECT * FROM payment_logs A WHERE id = (SELECT MAX(id) FROM payment_logs B WHERE A.id=B.id) and status = 1) AS success_payment'), function($join) {
            $join->on('bills.id', '=', 'success_payment.bill_id');
        })
        ->select(
            'bills.id as id',
            'bills.number as number',
            'bills.customer_name as customer_name',
            'bills.user_id as user_id',
            'users.business_name_en as user_business_name_en',
            'users.business_name_ar as user_business_name_ar',
            'bills.source as source',
            'success_payment.id as success_payment_id',
            'success_payment.payment_method as success_payment_payment_method',
            'success_payment.results as success_payment_results',
            'success_payment.brand as success_payment_brand',
            'bills.total as total',
            'bills.pricing as pricing',
            'bills.payment_fees as payment_fees',
            'bills.payment_fees_vat as payment_fees_vat',
            'bills.payment_surebills_fees as payment_surebills_fees',
            'bills.payment_surebills_fees_vat as payment_surebills_fees_vat',
            'bills.status as status',
            'bills.refund_amount as refund_amount',
            'channels.name as channel_name',
            'channels.user_id as channel_user_id',
            'bills.payment_channel_fees as payment_channel_fees',
            'bills.payment_channel_fees_vat as payment_channel_fees_vat',
            'bills.paid_at as paid_at'
        );


        if($this->filter['status'] != null){
            $billssQuery = $billssQuery->whereIn('bills.status', $this->filter['status']);
        }


        if($this->filter['application_id'] == 1){
            $billssQuery = $billssQuery->whereNotNull('bills.application_id');
        }elseif($this->filter['application_id'] == 2){
            $billssQuery = $billssQuery->whereNull('bills.application_id');
        }

        if($this->filter['created_at'] != null){
            $from = Carbon::parse($this->filter['created_at'][0])->startOfDay();
            $to = Carbon::parse($this->filter['created_at'][1])->endOfDay();

            $billssQuery = $billssQuery->whereBetween('bills.created_at', [$from, $to]);
        }

        if($this->filter['paid_at'] != null){
            $from = Carbon::parse($this->filter['paid_at'][0])->startOfDay();
            $to = Carbon::parse($this->filter['paid_at'][1])->endOfDay();

            $billssQuery = $billssQuery->whereBetween('bills.paid_at', [$from, $to]);
        }

        if($this->filter['refunded_at'] != null){
            $from = Carbon::parse($this->filter['refunded_at'][0])->startOfDay();
            $to = Carbon::parse($this->filter['refunded_at'][1])->endOfDay();

            $billssQuery = $billssQuery->whereBetween('bills.refunded_at', [$from, $to]);
        }

        if($this->filter['user_id'] != null){
            $billssQuery = $billssQuery->where('bills.user_id', $this->filter['user_id']);
        }
        // dd($billssQuery->toSql());
        return $billssQuery;
    }

    public function getPaymentMethodDetails($bill)
    {
        $method = '';

        if ($bill->success_payment_id == null) {
            return $method;
        }

        if ($bill->success_payment_payment_method == 'hyperpay_applepay') {
            $method .= 'APPLE PAY - ';
        }

        if($bill->success_payment_results){
            $success_payment_results = json_decode($bill->success_payment_results, true);
            if (isset($success_payment_results['response']) && isset($success_payment_results['response']['paymentBrand'])) {
                $method .= $success_payment_results['response']['paymentBrand'];
            }
        }

        if ($bill->success_payment_brand) {
            $method .= $bill->success_payment_brand;
        }

        return $method;
    }

}
