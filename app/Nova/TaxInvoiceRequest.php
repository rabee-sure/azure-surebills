<?php

namespace App\Nova;

use App\Nova\Actions\RequestProccess;
use App\Nova\Actions\RequestSent;
use App\Nova\Filters\RequestStatus;
use App\Nova\Filters\UserName;
use Illuminate\Http\Request;
use Inspheric\Fields\Indicator;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Pdmfc\NovaFields\ActionButton;
use PosLifestyle\DateRangeFilter\DateRangeFilter;

class TaxInvoiceRequest extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\TaxInvoiceRequest::class;

    public static $displayInNavigation = false;

    public static function label()
    {
        return __('Tax Invoice Requests');
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
            BelongsTo::make(__('Merchant'), 'user', User::class)->display(function () {
                return $this->user->business_name_en;
            }),
            Text::make(__('Email'), function () {
                return $this->user->email;
            }),
            
            Indicator::make(__('Request status'), 'status')
            ->labels([
                'pending' => __('pending'),
                'sent' => __('sent'),
            ])
            ->colors([
                'pending' => 'warning',
                'sent' => 'green',
            ]),
            
            DateTime::make(__('Created At'), 'created_at'),
            DateTime::make(__('Sent At'), 'sent_at'),

            ActionButton::make(__('Sent'))
            ->action(RequestSent::class, $this->id)
            ->readonly(function () {
                return $this->status == 'sent';
            })
            ->text(__('Sent'))
            ->buttonColor('#337ab7')
            ->showLoadingAnimation()
            ->loadingColor('#fff'),
        ];
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
            new DateRangeFilter(__('Created At'), 'created_at'),
            new DateRangeFilter(__('Sent At'), 'sent_at'),
            new UserName(),
            new RequestStatus(),
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
            (new RequestSent)->canRun(function (NovaRequest $request) {
                return true;
            }),
        ];
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
}
