<?php

namespace App\Nova;

use App\Models\Role;
use App\Nova\Filters\FilerAdminUserViaRole;
use App\Rules\PasswordRule;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
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
    public static $displayInNavigation = false;

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
        'name',
        'email',
    ];

    public static function label()
    {
        return __('users');
    }

    public static function singularLabel()
    {
        return __('user');
    }


    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $roles = $this->roles();
        return [
            ID::make(__('ID'), 'id')->sortable(),

            Text::make(__('Name'), 'name')->rules('required', 'string', 'max:50'),

            Text::make(__('Email'), 'email')
                ->rules('required', 'string', 'email', 'max:50')
                ->creationRules('unique:admins,email,NULL,id,deleted_at,NULL')
                ->updateRules('unique:admins,email,'.$this->id.',id,deleted_at,NULL')
                ->hideWhenUpdating(),

            

            Text::make(__('Mobile'), 'mobile')->rules('required', 'regex:/(^[5]{1}[0-9]{8}$)/')
                ->creationRules('unique:admins,mobile,NULL,id,deleted_at,NULL')
                ->updateRules('unique:admins,mobile,'.$this->id.',id,deleted_at,NULL'),

            Text::make(__('roles'), function() use ($roles){
                return $roles['selected_role'] ? $roles['admin_roles'][$roles['selected_role']] : '-';
            })->exceptOnForms(),

            Select::make(__('role'), 'role')
                ->options($roles['admin_roles'])
                ->withMeta(['value' => $roles['selected_role']])
                ->rules('required')
                ->onlyOnForms(),

            Boolean::make(__('is_active?'), 'is_active'),
            Boolean::make(__('password_block?'), 'password_block')->exceptOnForms(),
        ];
    }

    private function roles()
    {
        $adminRoles = [];
        $selectRole = null;
        $roles = Role::where('guard_name', 'admins')->get();
        foreach($roles as $role)
        {
            $adminRoles[$role->id] = $role->name;
            if(isset($this->id) && isset($this->roles->first()->id) && $this->roles->first()->id == $role->id)
            {
                $selectRole = $role->id;
            }
        }

        return ['admin_roles' => $adminRoles, 'selected_role' => $selectRole];
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
            new FilerAdminUserViaRole,
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
}
