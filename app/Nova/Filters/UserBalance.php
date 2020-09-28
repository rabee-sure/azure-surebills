<?php

namespace App\Nova\Filters;

use App\User;
use DigitalCreative\RangeInputFilter\RangeInputFilter;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class UserBalance extends RangeInputFilter 
{
    /**
     * Get the displayable name of the metric.
     *
     * @return string
     */
    public function name()
    {
        return  __('Balance');
    }

    public function apply(Request $request, $query, $value)
    {
        if(is_array($value) ){
            if($value["from"] > 0 || (isset($value["to"]) && $value["to"] > 0) )
            $filtered_ids = $query->get()->filter(function($model) use($value){
                if($value["from"] > 0 &&  $value["to"] > 0){
                    return $model->round_balance >= $value["from"] && $model->round_balance <= $value["to"];
                }
                elseif($value["from"] > 0){
                    return $model->round_balance >= $value["from"];
                }elseif($value["to"] > 0){
                    return $model->round_balance <= $value["to"];
                }
            })->pluck('id')->toArray();

            return $query->whereIn('id', $filtered_ids);
        }
        return $query;
    }
    
    public function options(Request $request) : array
    {
        return [
            'fromPlaceholder' => 1000,
            'toPlaceholder' => 2000,
            'dividerLabel' => 'to',
        ];
    }

}