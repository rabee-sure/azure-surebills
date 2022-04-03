<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class DueAmountAutoTransferReport extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */

    public static $model = \App\Models\DueAmountAutoTransferReport::class;
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
    public static $search = [
        'auto_transfer_id'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Text::make('merchant_id'),
            Text::make('merchant_name'),
            Text::make('merchant_iban'),
            Text::make('bank'),
            Text::make('total_amount'),
            Text::make('total_fees'),
            Text::make('total_fees_vat'),
            Text::make('total_refund'),
            Text::make('bank_charges'),
            Text::make('net_due'),
            Text::make('channel_id'),
            Text::make('reference'),
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
