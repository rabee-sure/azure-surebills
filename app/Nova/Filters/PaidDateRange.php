<?php
namespace App\Nova\Filters;

use Ampeco\Filters\DateRangeFilter;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaidDateRange extends DateRangeFilter
{
    /**
     * Get the displayable name of the metric.
     *
     * @return string
     */
    public function name()
    {
        return  __('Paid at Date Range');
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
        $from = Carbon::parse($value[0])->startOfDay();
        $to = Carbon::parse($value[1])->endOfDay();

        return $query->whereBetween('paid_at', [$from, $to]);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    // public function options(Request $request)
    // {
    //     return [
    //         'firstDayOfWeek' => 0,
    //         'separator' => '-',
    //         'enableTime' => false,
    //         'enableSeconds' => false,
    //         'twelveHourTime' => false
    //     ];
    // }
}