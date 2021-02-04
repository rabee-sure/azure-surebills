<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class UsersVerified extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';
    
    /**
     * Get the displayable name of the metric.
     *
     * @return string
     */
    public function name()
    {
        return  __('Settled');
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
        if($value == 1){
            return $query->where('verified', true);
        }else if($value == 3){
            $query->where('verified', false)
                ->whereNotNull(['business_name_en',
                    'business_address',
                    'business_mobile'
                ]);
        }
        else{
            return $query->where('verified', false);
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
            __('verified') => 1,
            __('Un Verified') => 2,
            __('Un Verified + Complete Profile') => 3,
        ];
    }
}
