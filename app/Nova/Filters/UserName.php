<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class UserName extends Filter
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
        return  __('Merchant Name');
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
        return $query->where('user_id', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return \App\Models\User::query()
            ->whereHas('transfers')
            ->select(['id', 'name', 'business_name_en'])
            ->orderBy('business_name_en')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function ($user) {
                $label = trim((string) ($user->business_name_en ?: $user->name));

                if ($label === '') {
                    $label = __('Merchant') . ' #' . $user->id;
                }

                // Keep labels unique so options are not overwritten on duplicate names.
                return [$label . ' (#' . $user->id . ')' => $user->id];
            })
            ->all();
    }
}
