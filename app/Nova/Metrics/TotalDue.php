<?php

namespace App\Nova\Metrics;

use App\Bill;
use App\Transfer;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class TotalDue extends Value
{
    /**
     * Get the displayable name of the metric.
     *
     * @return string
     */
    public function name()
    {
        return  __('Total Due');
    }

    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
       $total_income = Bill::where('status', 'paid')->sum('total');
       $total_commissions = Bill::where('status', 'paid')->sum('payment_fees');
       $total_paid = Transfer::sum('amount');
       // dd([
       //  $total_income,
       //  $total_commissions,
       //  $total_paid,
       // ]);
        return $this->result($total_income - $total_commissions - $total_paid)->previous(50);;
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            'all' => __('All time')
        ];
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'total-due-transfer';
    }
}
