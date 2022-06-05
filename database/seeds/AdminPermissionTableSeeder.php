<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

class AdminPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach(config('AdminRolePermissionsMatrix') as $permission)
        {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admins'], ['name' => $permission, 'guard_name' => 'admins']);
        }

        Permission::whereNotIn('name', config('AdminRolePermissionsMatrix'))
            ->where('guard_name', 'admins')
            ->delete();

        Artisan::call('db:seed --class=CreateAdminSuperAdminUserSeeder');
        dd('success');
    }
}
