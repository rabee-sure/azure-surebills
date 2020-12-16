<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Channel extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Channel::class;

    /**
     * Get the displayble label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Channels');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('Channel');
    }
    
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    // public static $displayInNavigation = false;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

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
            ID::make()->sortable(),
            Text::make(__('Name'), 'name')->rules('required'),
            BelongsTo::make(__('User'), 'user', User::class)->searchable()->rules('required'),
            HasMany::make(__('Applications'), 'applications', Application::class)->rules('required'),
            Boolean::make(__('Active'), 'activate')->rules('required'),
            Number::make(__('Mada fixed fees'), 'mada_fixed')
                ->rules('required', 'numeric', 'max:1000')
                ->step(0.1)
                ->hideFromIndex(),
            Number::make(__('Mada percentage fees'), 'mada_percentage')
                ->rules('required', 'numeric', 'max:100')
                ->step(0.1)
                ->hideFromIndex(),
            Number::make(__('Credit Card fixed fees'), 'credit_cards_fixed')
                ->rules('required', 'numeric', 'max:1000')
                ->step(0.1)
                ->hideFromIndex(),
            Number::make(__('Credit Card percentage fees'), 'credit_cards_percentage')
                ->rules('required', 'numeric', 'max:100')
                ->step(0.1)
                ->hideFromIndex(),

            DateTime::make(__('Created At'), 'created_at')
                // ->hideFromIndex()
                ->exceptOnForms(),

        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return arr
     ay
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
