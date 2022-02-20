<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

use rcknr\Nova\Filters\MultiselectFilter;

class BillStatus extends MultiselectFilter
{
    /**
     * Get the displayable name of the metric.
     *
     * @return string
     */
    public function name()
    {
        return  __('Status');
    }

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->whereIn('status', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return [
            __('Pending') => 'pending',
            __('Paid') => 'paid',
            __('Canceled') => 'canceled',
            __('Failed') => 'failed',
            __('Expired') => 'expired',
            __('Refunded') => 'refunded',
            __('Refunded Cash') => 'refunded_cash',
            __('Refunded Bank Transfer') => 'refunded_bank_transfer',
            __('Paid Cash') => 'paid_cash',
            __('Paid Bank Transfer') => 'paid_bank_transfer',
        ];
    }
}
