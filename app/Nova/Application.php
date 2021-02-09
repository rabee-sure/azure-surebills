<?php

namespace App\Nova;

use App\Rules\MinValueOfChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Spatie\NovaTranslatable\Translatable;

class Application extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Application::class;

    public static $displayInNavigation = false;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */    
    public static function searchable()
    {
        return true;
    }

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * Get the displayble label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Applications');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('Application');
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name'
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
            Text::make(__('Name'), 'name'),
            BelongsTo::make(__('User'), 'user', User::class)
            ->searchable()
            ->rules('required'),            
            BelongsTo::make(__('Channel'), 'channel', Channel::class),

            Text::make(__('Redirect Url'), 'redirect')
                ->rules('required', 'url'),
            Text::make(__('Webhook URL'), 'webhook_url')
                ->rules('required', 'url'),

            new Panel(__('Pricing'), $this->pricingFields()),
        ];
    }

    /**
     * Get the address fields for the resource.
     *
     * @return array
     */
    protected function pricingFields()
    {
        return [
            Number::make(__('Mada fixed fees'), 'mada_fixed')
                ->rules('required', 'numeric', 'max:1000', new MinValueOfChannel())
                ->step(0.01)
                ->hideFromIndex(),
            Number::make(__('Mada percentage fees'), 'mada_percentage')
                ->rules('required', 'numeric', 'max:100', new MinValueOfChannel())
                ->step(0.01)
                ->hideFromIndex(),
            Number::make(__('Credit Card fixed fees'), 'credit_cards_fixed')
                ->rules('required', 'numeric', 'max:1000', new MinValueOfChannel())
                ->step(0.01)
                ->hideFromIndex(),
            Number::make(__('Credit Card percentage fees'), 'credit_cards_percentage')
                ->rules('required', 'numeric', 'max:100', new MinValueOfChannel())
                ->step(0.01)
                ->hideFromIndex(),
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

    // public static function authorizedToCreate(Request $request)
    // {
    //     return false;
    // }
    
    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

    public static function newModel()
    {
        $model = static::$model;
        $instance = new $model;

        if ($instance->secret == null) {
            $instance->secret = Str::random(20);
        }        
        if ($instance->webhook_secret == null) {
            $instance->webhook_secret = Str::random(20);
        }
        return $instance;
    }
}
