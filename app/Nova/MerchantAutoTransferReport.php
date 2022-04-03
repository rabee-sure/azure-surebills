<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class MerchantAutoTransferReport extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */

    public static $model = \App\Models\MerchantAutoTransferReport::class;
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
            Text::make('created_at'),
            Text::make('description'),
            Text::make('type'),
            Text::make('amount'),
            Text::make('transaction_id'),
            Text::make('bill_id'),
            Text::make('bill_reference_id'),
            Text::make('bill_number'),
            Text::make('bill_user_id'),
            Text::make('bill_business_name'),
            Text::make('card_brand'),
            Text::make('card'),
            Text::make('source'),
            Text::make('bill_application_channel_id'),
            Text::make('bill_application_channel_name'),
        ];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        if($request->search)
        {
            return $query->where([['auto_transfer_id', $request->search], ['report_type', 'merchants_transactions']]);
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
