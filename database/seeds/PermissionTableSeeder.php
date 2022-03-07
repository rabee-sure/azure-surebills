<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach(config('RolePermissionsMatrix') as $permission)
        {
            Permission::firstOrCreate(['name' => $permission], ['name' => $permission]);
        }

        Permission::whereNotIn('name', config('RolePermissionsMatrix'))->delete();

        Artisan::call('db:seed --class=CreateSuperAdminUserSeeder');
        dd('success');
    }
}
