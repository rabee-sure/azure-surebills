<?php

namespace App\Nova;

use App\Nova\Filters\BillSource;
use App\Nova\Filters\BillStatus;
use App\Nova\Filters\DateRange;
use App\Nova\Filters\UserId;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Titasgailius\SearchRelations\SearchesRelations;

class Bill extends Resource
{
    use SearchesRelations;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Bill::class;

    /**
     * Get the displayble label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Bills');
    }


    /**
     * Remove Zero Transactions rubbish from database
     * @Todo : Remember to retrieve also here all transactions that don't relate to reservation
     * @param NovaRequest $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    // public static function indexQuery(NovaRequest $request, $query)
    // {
    //     $filters = json_decode(base64_decode(\request('filters')), true);

    //     dd( $filters);
    //     collect($filters)->each(function ($filter) use ($request, $query) {
    //         if (!is_null($filter['value']) and !empty($filter['value'])) {
    //             (new $filter['class'])->apply($request, $query, $filter['value']);
    //         }
    //     });

    //     return $query;
    // }


    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The relationship columns that should be searched.
     *
     * @var array
     */
    public static $searchRelations = [
        'customer' => ['name', 'mobile'],
    ];

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'number',
    ];

    /**
     * order By.
     *
     * @var array
     */
    public static $orderBy = [
        'created_at' => 'desc'
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
            Text::make('Name', function () {
                return __('Bill').' '.  $this->number  .'-'. $this->customer_name;
            }),
            Badge::make('Status')->map([
                'pending' => 'info',
                'paid' => 'success',
                'canceled' => 'warning',
                'expired' => 'danger',
            ]),         
            Select::make('Payment Method')->options([
                'credit' => 'credit',
                'stc' => 'stc',
                'apple' => 'apple',
            ]),

            Number::make('Total')->min(1)->step(0.1),
            Number::make('Payment Fees')->min(1)->step(0.1),
            Number::make('Discount')->min(1)->step(0.1)->onlyOnDetail(),
            Number::make('Vat')->min(1)->step(0.1)->onlyOnDetail(),
            BelongsTo::make('User'),
            DateTime::make('created at')->exceptOnForms(),
            BelongsTo::make('Customer')->onlyOnDetail(),
            Text::make('Business Name')->onlyOnDetail(),
            Text::make('Reference Id')->onlyOnDetail(),
            Date::make('Due Date')->onlyOnDetail(),
            DateTime::make('Paid At')->onlyOnDetail(),
            DateTime::make('Canceled At')->onlyOnDetail(),

            Boolean::make('Send Email')->onlyOnDetail(),
            Boolean::make('Send Sms')->onlyOnDetail(),
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
            new BillStatus,
            new BillSource,
            new DateRange,
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
        return [];
    }
    
    /**
     * authorized To Create.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return boolean
     */
    public static function authorizedToCreate(Request $request)
    {
        return false;
    }
    
    /**
     * authorized To Delete.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return boolean
     */
    public function authorizedToDelete(Request $request)
    {
        return false;
    }
    
    /**
     * authorized To Update.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return boolean
     */
    public function authorizedToUpdate(Request $request)
    {
        return false;
    }


}
