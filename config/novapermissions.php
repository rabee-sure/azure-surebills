<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User model class
    |--------------------------------------------------------------------------
    */

    'user_model' => 'App\Models\Admin',
    'userModel' => 'App\Models\Admin',

    /*
    |--------------------------------------------------------------------------
    | Nova User resource tool class
    |--------------------------------------------------------------------------
    */

    'user_resource' => 'App\Nova\Admin',
    'userResource' => 'App\Nova\Admin',

    /*
    |--------------------------------------------------------------------------
    | The group associated with the resource
    |--------------------------------------------------------------------------
    */

    'role_resource_group' => 'Other',
    'roleResourceGroup' => 'Other',

    /*
    |--------------------------------------------------------------------------
    | Database table names
    |--------------------------------------------------------------------------
    | When using the "HasRoles" trait from this package, we need to know which
    | table should be used to retrieve your roles. We have chosen a basic
    | default value but you may easily change it to any table you like.
    */

    'table_names' => [
        'roles' => 'admin_roles',

        'role_permission' => 'admin_role_permission',

        'role_user' => 'role_admin',

        'users' => 'admins',
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Permissions
    |--------------------------------------------------------------------------
    */

    'permissions' => [
        // 'view users' => [
        //     'display_name' => 'View users',
        //     'description'  => 'Can view users',
        //     'group'        => 'User',
        // ],

        // 'create users' => [
        //     'display_name' => 'Create users',
        //     'description'  => 'Can create users',
        //     'group'        => 'User',
        // ],

        // 'edit users' => [
        //     'display_name' => 'Edit users',
        //     'description'  => 'Can edit users',
        //     'group'        => 'User',
        // ],

        // 'delete users' => [
        //     'display_name' => 'Delete users',
        //     'description'  => 'Can delete users',
        //     'group'        => 'User',
        // ],

        /*
        show roles
        create role
        edit role
        delete role
        */

        'show roles' => [
            'display_name' => 'View roles',
            'description'  => 'Can view roles',
            'group'        => 'Role',
        ],

        'create role' => [
            'display_name' => 'Create roles',
            'description'  => 'Can create roles',
            'group'        => 'Role',
        ],

        'edit role' => [
            'display_name' => 'Edit roles',
            'description'  => 'Can edit roles',
            'group'        => 'Role',
        ],

        'delete role' => [
            'display_name' => 'Delete roles',
            'description'  => 'Can delete roles',
            'group'        => 'Role',
        ],
    ],
];
