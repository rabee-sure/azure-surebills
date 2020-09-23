<?php
namespace App\Nova\Filters;

use Ampeco\Filters\DateRangeFilter;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DateRange extends DateRangeFilter
{
    public function apply(Request $request, $query, $value)
    {
        $from = Carbon::parse($value[0])->startOfDay();
        $to = Carbon::parse($value[1])->endOfDay();

        return $query->whereBetween('created_at', [$from, $to]);
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