<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class UserTypeFilter extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    public function name()
    {
        return  __('user type');
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
        if($value == 'owner')
        {
            return $query->where('store_main_user_id', null);
        }
        else
        {
            return $query->whereNotNull('store_main_user_id');
        }
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
            __('owner') => 'owner',
            __('employee') => 'employee',
        ];
    }
}
