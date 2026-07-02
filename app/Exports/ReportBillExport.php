<?php

namespace App\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomQuerySize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportBillExport implements FromQuery, WithHeadings, ShouldQueue, WithCustomQuerySize
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
            'MID',
            'Merchant_Name',
            'Payment_gateway_ID',
            'paid_at',
            'status',
            'Channel_Name',
            'total',
            'Card_type',
            'vat_percentage',
            'Total_Fees',
            'Total_Fees_VAT',
            'total_fees_fixed',
            'total_fees_percentage',
            'Channel_Fees',
            'Channel_Fees_VAT',
            'channel_fees_fixed',
            'channel_fees_percentage',
            'Surebills_Fees',
            'Surebills_Fees_VAT',
            'surebills_fees_fixed',
            'surebills_fees_percentage',
            'refund_amount',
            'Transfer_id'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $report_filters = $this->filter;
        
        $query = DB::table('bills')
        ->leftJoin('users', 'bills.user_id', '=', 'users.id')
        ->leftJoin('payment_logs', function($join)
            {
                $join->on('bills.id', '=', 'payment_logs.bill_id');
                $join->on('payment_logs.status','=', DB::raw('1'));
            })
        ->leftJoin('applications', 'bills.application_id', '=', 'applications.id')
        ->leftJoin('channels', 'applications.channel_id', '=', 'channels.id')
        ->leftJoin('transactions', 'bills.id', '=', 'transactions.bill_id')
        ->leftJoin('transaction_transfer', 'transactions.id', '=', 'transaction_transfer.transaction_id')
        ->select(
            "bills.user_id as MID",
            "users.business_name_en as Merchant_Name",
            "bills.id as Payment_gateway_ID",
            "bills.paid_at",
            "bills.status",
            "channels.name as Channel_Name",
            "bills.fixed_total",
            "payment_logs.brand as Card_type",
            DB::raw("JSON_EXTRACT(bills.pricing, '$.vat_percentage') as vat_percentage"),
            "bills.payment_fees as Total_Fees",
            "bills.payment_fees_vat as Total_Fees_VAT",
            DB::raw("JSON_EXTRACT(bills.pricing, '$.fees_fixed') as total_fees_fixed"),
            DB::raw("JSON_EXTRACT(bills.pricing, '$.fees_percentage') as total_fees_percentage"),
            "bills.payment_channel_fees as Channel_Fees",
            "bills.payment_channel_fees_vat as Channel_Fees_VAT",
            DB::raw("JSON_EXTRACT(bills.pricing, '$.channel_fees_fixed') as channel_fees_fixed"),
            DB::raw("JSON_EXTRACT(bills.pricing, '$.channel_fees_percentage') as channel_fees_percentage"),
            "bills.payment_surebills_fees as Surebills_Fees",
            "bills.payment_surebills_fees_vat as Surebills_Fees_VAT",
            DB::raw("JSON_EXTRACT(bills.pricing, '$.surebills_fees_fixed') as surebills_fees_fixed"),
            DB::raw("JSON_EXTRACT(bills.pricing, '$.surebills_fees_percentage') as surebills_fees_percentage"),
            "bills.refund_amount",
            "transaction_transfer.transfer_id as Transfer_id"
        )
        ->whereDate('paid_at', '>=', $report_filters['paid_from'])
        ->whereDate('paid_at', '<=', $report_filters['paid_to'])
        ->whereIn('bills.status', ['paid', 'refunded', 'rejected']);

        $this->applyMerchantChannelFilters($query, $report_filters);

        $query->groupBy('bills.id')->orderBy('paid_at');

        return $query;
    }

    public function querySize(): int
    {
        $report_filters = $this->filter;

        $query = DB::table('bills')
        ->leftJoin('users', 'bills.user_id', '=', 'users.id')
        ->leftJoin('payment_logs', function($join)
            {
                $join->on('bills.id', '=', 'payment_logs.bill_id');
                $join->on('payment_logs.status','=', DB::raw('1'));
            })
        ->leftJoin('applications', 'bills.application_id', '=', 'applications.id')
        ->leftJoin('channels', 'applications.channel_id', '=', 'channels.id')
        ->leftJoin('transactions', 'bills.id', '=', 'transactions.bill_id')
        ->leftJoin('transaction_transfer', 'transactions.id', '=', 'transaction_transfer.transaction_id')
        ->select(
            "bills.id as BID"
        )
        ->whereDate('paid_at', '>=', $report_filters['paid_from'])
        ->whereDate('paid_at', '<=', $report_filters['paid_to'])
        ->whereIn('bills.status', ['paid', 'refunded', 'rejected']);

        $this->applyMerchantChannelFilters($query, $report_filters);

        $query->groupBy('bills.id')->orderBy('paid_at');

        $countQuery = "select count(*) as aggregate from ({$query->toSql()}) c";

        $count = collect(DB::select($countQuery, $query->getBindings()))->pluck('aggregate')->first();

        return $count;
    }

    /**
     * @param  \Illuminate\Database\Query\Builder  $query
     */
    private function applyMerchantChannelFilters($query, array $report_filters)
    {
        $merchantIds = $this->parseFilterIds(isset($report_filters['merchants']) ? $report_filters['merchants'] : '');
        $channelIds = $this->parseFilterIds(isset($report_filters['channels']) ? $report_filters['channels'] : '');

        if ($merchantIds === [] && $channelIds !== []) {
            $query->whereIn('channels.id', $channelIds);
        } elseif ($merchantIds !== [] && $channelIds === []) {
            $query->whereIn('bills.user_id', $merchantIds);
        } elseif ($merchantIds !== [] && $channelIds !== []) {
            $query->where(function ($innerQuery) use ($merchantIds, $channelIds) {
                $innerQuery->whereIn('bills.user_id', $merchantIds)
                    ->orWhereIn('channels.id', $channelIds);
            });
        }
    }

    private function parseFilterIds($value)
    {
        if ($value === null || $value === '' || $value === 'all') {
            return [];
        }

        return array_values(array_filter(
            explode(',', str_replace('"', '', $value)),
            function ($id) {
                return $id !== '' && $id !== 'all';
            }
        ));
    }
}
