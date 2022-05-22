<?php

namespace Database\Seeders;

use App\Models\Admin;
use Exception;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class CreateAdminSuperAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admins = Admin::all();
        $role = Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'admins'], ['name' => 'super admin', 'guard_name' => 'admins']);

        foreach($admins as $admin)
        {
            $permissions = Permission::where('guard_name', 'admins')->pluck('id')->all();
            $role->syncPermissions($permissions);
            $admin->assignRole($role->id);
        }
    }
}
