<?php

namespace Database\Seeders;

use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class CreateSuperAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::whereNull('store_main_user_id')->get();
        $role = Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'web'], ['name' => 'super admin', 'guard_name' => 'web']);

        $permissions = Permission::where('guard_name', 'web')->pluck('id')->all();
        $role->syncPermissions($permissions);

        foreach($users as $user)
        {
            $user->assignRole($role->id);
        }
    }
}
