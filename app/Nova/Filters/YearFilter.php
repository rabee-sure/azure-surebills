<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Laravel\Nova\Filters\Filter;
use Illuminate\Support\Facades\DB;


class YearFilter extends Filter
{
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
        return $query->where(DB::raw('YEAR(transactions.created_at)'), '=', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        $firstYear = 2020;
        $lastYear = date("Y");
        $yearsOptions = [];
        
        for($y = $firstYear; $y <= $lastYear; $y++){
            $yearsOptions[$y] = $y;
        }
        return $yearsOptions;
    }
}
