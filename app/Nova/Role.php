<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spatie\Permission\Models\Permission;
use Benjacho\BelongsToManyField\BelongsToManyField;
use Laravel\Nova\Fields\BelongsTo;
use Fourstacks\NovaCheckboxes\Checkboxes;

class Role extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Role::class;
    public static $displayInNavigation = false;
    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    public static function label()
    {
        return __('roles');
    }

    public static function singularLabel()
    {
        return __('role');
    }
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

            Text::make(__('Name'), 'name')
                ->rules('required', 'string', 'max:50')
                ->creationRules('unique:roles,name,NULL,id,guard_name,admins')
                ->updateRules('unique:roles,name,'.$this->id.',id,guard_name,admins')
                ,

            Hidden::make('guard_name')->default(function ($request) {
                return 'admins';
            }),

            Checkboxes::make(__('Permissions'), 'admin_permissions')
                ->options($this->adminPermissions())
                ->rules('required')
                ->columns(3)
                ->hideFromIndex(),
        ];
    }

    private function adminPermissions()
    {
        $permissions = [];
        foreach(config('AdminRolePermissionsMatrix') as $permission)
        {
            $permissionId = Permission::where([['name', $permission], ['guard_name', 'admins']])->pluck('id')->first();
            $permissions[$permissionId] = __($permission);
        }

        return $permissions;
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->where('guard_name', 'admins')->where('name', '<>', 'super admin');
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
