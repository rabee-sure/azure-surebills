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
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('Bill');
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
            Text::make(__('Name'), function () {
                return __('Bill').' '.  $this->number  .'-'. $this->customer_name;
            }),
            Badge::make(__('Status'), 'status')->map([
                'pending' => 'info',
                'paid' => 'success',
                'canceled' => 'warning',
                'expired' => 'danger',
            ]),

            Text::make(__('Url') , 'pay_url')->displayUsing(function(){
                return '<a href="'.$this->pay_url.'" target="_blank" class="no-underline dim text-primary  view_reservation">' . __('Bill link') . '</a>';
            })->sortable()->onlyOnDetail()->asHtml(),

            BelongsTo::make(__('Application'), 'application', Application::class)->onlyOnDetail(), 

            Select::make(__('Payment Method'), 'payment_method')->options([
                'credit' => 'credit',
                'stc' => 'stc',
                'apple' => 'apple',
            ]),

            Number::make(__('Total'), 'total')->min(1)->step(0.1),
            Number::make( __('Payment Fees'), 'payment_fees')->min(1)->step(0.1),
            Number::make(__('discount'), 'discount')->min(1)->step(0.1)->onlyOnDetail(),
            Number::make(__('Tax'), 'vat')->min(1)->step(0.1)->onlyOnDetail(),
            DateTime::make(__('Created At'), 'created_at')->exceptOnForms(),
            BelongsTo::make(__('User'), 'user', User::class),
            BelongsTo::make(__('Customer'), 'customer', Customer::class)->onlyOnDetail(),
            Text::make(__('Business Name'), 'business_name')->onlyOnDetail(),
            Text::make(__('Reference Id'), 'reference_id')->onlyOnDetail(),
            Date::make(__('Due Date'), 'due_date')->onlyOnDetail(),
            DateTime::make(__('Paid At'), 'paid_at')->onlyOnDetail(),
            DateTime::make(__('Canceled At'), 'canceled_at')->onlyOnDetail(),

            Boolean::make(__('Send Email'), 'send_email')->onlyOnDetail(),
            Boolean::make(__('Send Sms'), 'send_sms')->onlyOnDetail(),
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
