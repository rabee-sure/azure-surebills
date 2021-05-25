<?php

namespace App\Nova;

use App\Nova\Filters\DateRange;
use App\Nova\Filters\UserId;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;
use Inspheric\Fields\Indicator;

class Statement extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Transaction::class;


    public static $displayInNavigation = false;


    /**
     * Get the displayble label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Statement');
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
        'receipt', 'description', 'reference'
    ];

    /**
     * order By.
     *
     * @var array
     */
    public static $orderBy = [
        'created_at' => 'ASC',
        'order' => 'ASC',
        'receipt' => 'ASC',
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
            // DateTime::make(__('Created At'), 'created_at')->exceptOnForms(),
            Text::make(__('Created At'), 'created_at')
                ->displayUsing(function(){
                    if($this->created_at)
                        return $this->created_at->format('Y-m-d h:i a');
                    else
                        return 'NULL';
                })->exceptOnForms(),
                
            Text::make(__('Description'), 'description'),
            Text::make(__('Reference'), 'reference'),
            Text::make(__('Receipt'), 'receipt', function(){
                return ' '.$this->receipt;
            }),
            Text::make(__('Auth ID'), 'auth_id'),
            Select::make(__('Card Brand'), 'card_brand')->options([
                'VISA' => 'VISA',
                'MASTER' => 'MASTER',
                'MADA' => 'MADA',
                'APPLEPAY' => 'APPLEPAY',
            ]),
            
            Indicator::make('type')
                ->labels([
                    'credit' => __('credit'),
                    'debit' => __('debit'),
                ])
                ->colors([
                    'debit' => 'red',
                    'credit' => 'green',
                ]),

            Text::make(__('Amount'), 'amount', function () {
                return round($this->amount, 2);
            }),

            Text::make(__('Balance'), 'balance', function () {
                return fact_number(round($this->balance, 2));
            }),

            BelongsTo::make(__('User'), 'user', User::class)->searchable(),
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
            new DateRange(),
            new UserId(),
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
            (new DownloadExcel)->withHeadings(),
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
