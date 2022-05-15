<?php

namespace App\Nova;

use App\Nova\Filters\YearFilter;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\TEXT;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Facades\DB;
use App\Nova\Actions\MerchantsExcelDownload;

class MerchantsReport extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\User::class;
    public static $displayInNavigation = false;

    /**
     * Get the displayble label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Merchants Reports');
    }

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
        'id',
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
            ID::make(__('ID'), 'id')->sortable(),
            TEXT::make(__('Merchant Name'), 'name')->exceptOnForms(),
            TEXT::make(__('Phone'), 'mobile')->exceptOnForms(),
            TEXT::make(__('Email'), 'email')->exceptOnForms(),
            TEXT::make(__('Business Name'), 'business_name_en')->exceptOnForms(),
            TEXT::make(__('Commercial Record'), 'vat_registration_number')->exceptOnForms(),
            TEXT::make(__('City'), 'city')->exceptOnForms(),
            TEXT::make(__('Address'), 'business_address')->exceptOnForms(),
            TEXT::make(__('Total transactions amount per Year'), 'Total_amounts', function () {
                return !is_null($this->Total_amounts) ? number_format($this->Total_amounts, 2, '.', ',') : 0;
            })->sortable()->onlyOnIndex(),
            Text::make(__('View Profile'), function(){
                return "<a class='btn btn-success' style='margin:5px' href='/nova/resources/users/".$this->id."'><i class='fa fa-eye' aria-hidden='true'></i></a>";
            })->asHtml()->onlyOnIndex(),
        ];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        // DB::raw("(SUM(CASE WHEN transactions.type  = 'credit' THEN transactions.amount ELSE 0 END) - SUM(CASE WHEN transactions.type  = 'debit' THEN transactions.amount ELSE 0 END)) AS Total_amounts")
        return $query
        ->join('transactions', 'users.id', '=', 'transactions.user_id')
        ->select('users.*', 'transactions.user_id', DB::raw("SUM(transactions.amount) AS Total_amounts"))
        ->where([['verified', true], ['store_main_user_id', null]])
        ->where('transactions.type', 'credit')
        ->groupBy('transactions.user_id');
    }

    public function authorizedToView(Request $request)
    {
        return false;
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
        return [
            new YearFilter
        ];
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
        return [
            (new MerchantsExcelDownload)->canRun(function (NovaRequest $request) {
                return true;
            }),
        ];
    }
}
