<?php

namespace App\Nova;

use App\Rules\PasswordRule;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Admin extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Admin::class;

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

    public static function label()
    {
        return __('system admins');
    }

    public static function singularLabel()
    {
        return __('system admin');
    }


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

            Text::make(__('Name'), 'name')->rules('required', 'string', 'max:50'),

            Text::make(__('Email'), 'email')
                ->rules('required', 'string', 'email', 'max:50')
                ->creationRules('unique:admins,email,NULL,id,deleted_at,NULL')
                ->updateRules('unique:admins,email,'.$this->id.',id,deleted_at,NULL'),

            Password::make(__('Password'), 'password')
                ->rules('string', 'min:8', new PasswordRule)
                ->creationRules('required')
                ->updateRules('nullable')
                ->onlyOnForms(),

            Text::make(__('Mobile'), 'mobile')->rules('required', 'regex:/(^[5]{1}[0-9]{8}$)/')
                ->creationRules('unique:admins,mobile,NULL,id,deleted_at,NULL')
                ->updateRules('unique:admins,mobile,'.$this->id.',id,deleted_at,NULL'),

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
}
