<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
        $role = Role::firstOrNew(['name' => 'super admin'], ['name' => 'super admin']);

        foreach($users as $user)
        {
            $permissions = Permission::pluck('id')->all();
            $role->syncPermissions($permissions);
            $user->assignRole([$role->id]);
        }
    }
}
