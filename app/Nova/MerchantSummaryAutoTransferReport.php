<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Events\AddActionLogEvent;
use Illuminate\Support\Facades\Auth;

class MerchantSummaryAutoTransferReport extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */

    public static $model = \App\Models\MerchantSummaryAutoTransferReport::class;
    public static $displayInNavigation = false;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Text::make('client_id'),
            Text::make('payment_type'),
            Text::make('no_of_trx'),
            Text::make('total_amount'),
            Text::make('total_fees'),
            Text::make('total_fees_vat'),
            Text::make('total_fees_variable_rate'),
            Text::make('total_fees_fixed_rate'),
            Text::make('sure_variable_rate'),
            Text::make('sure_fixed_rate'),
            Text::make('channel_variable_rate'),
            Text::make('channel_fixed_rate'),
            Text::make('sure_fees'),
            Text::make('sure_vat'),
            Text::make('channel_fees'),
            Text::make('channels_vat'),
            Text::make('channel_id'),
        ];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        if($request->search)
        {
            return $query->where('auto_transfer_id', $request->search);
        }

        return $query->where('auto_transfer_id', -1);
    }

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }
    public function authorizedToView(Request $request)
    {
        return false;
    }
    public static function searchable()
    {
        return false;
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
