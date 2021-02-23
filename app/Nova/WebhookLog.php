<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Titasgailius\SearchRelations\SearchesRelations;

class WebhookLog extends Resource
{
    use SearchesRelations;
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\WebhookLog::class;

    /**
     * Get the displayble label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('WebhookLogs');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('WebhookLog');
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
        'id', 'bill_id'
    ];

    /**
     * The relationship columns that should be searched.
     *
     * @var array
     */
    public static $searchRelations = [
        'user' => ['name'],
        'application' => ['name'],
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
            Boolean::make(__('Status'), 'staus'),
            BelongsTo::make(__('Bill'), 'bill', Bill::class)->searchable()->rules('required'),
            BelongsTo::make(__('Application'), 'application', Application::class)->searchable()->rules('required'),
            BelongsTo::make(__('User'), 'user', User::class)->searchable()->rules('required'),
            Text::make(__('Status Code'), 'status_code'),
            Text::make(__('Error Message'), 'error_message')
                ->displayUsing(function($error_message) {
                    $part = strip_tags(substr($error_message, 0, 30));
                    return $part . " ...";
                }),

            Code::make('response')->json(),
            Code::make('payload')->json(),


            DateTime::make(__('Created At'), 'created_at')->exceptOnForms(),

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
