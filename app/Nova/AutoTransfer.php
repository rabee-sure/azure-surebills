<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spatie\NovaTranslatable\Translatable;

class AutoTransfer extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\AutoTransfer::class;
    public static $displayInNavigation = false;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static function searchable()
    {
        return false;
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
        return __('AutoTransfers');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('AutoTransfer');
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

            Text::make(__('Day'), 'day'),

            Text::make(__('Zip File'), function ($model) {
                return "<a href='".Storage::disk('public')->url($model->zip_file)."'>{$model->zip_file}</a>";
            })->asHtml(),

            Text::make(__('Merchants File'), function ($model) {
                return "<a href='".Storage::disk('public')->url($model->merchants_file)."'>{$model->merchants_file}</a>";
            })->asHtml(),

            Text::make(__('Channels File'), function ($model) {
                return "<a href='".Storage::disk('public')->url($model->channels_file)."'>{$model->channels_file}</a>";
            })->asHtml(),

            Text::make(__('Due Amount File'), function ($model) {
                return "<a href='".Storage::disk('public')->url($model->due_amount_file)."'>{$model->due_amount_file}</a>";
            })->asHtml(),

            Text::make(__('Merchants Summary File'), function ($model) {
                return "<a href='".Storage::disk('public')->url($model->merchants_summary_file)."'>{$model->merchants_summary_file}</a>";
            })->asHtml(),

            BelongsToMany::make(__('Transfers'), 'transfers', Transfer::class),
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
