<?php

namespace Database\Seeders;

use App\Models\User;
use Exception;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class CreatePosSuperAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $posPermissions = ['show pos','show customers','show products','create product','update product','delete product','show product categories',
                            'create product category','update product category','delete product category','update business commercial info',
                            'show payment record'];

        $users = User::where('source', 'pos')->get();
        $role = Role::firstOrCreate(['name' => 'pos super admin', 'guard_name' => 'web'], ['name' => 'pos super admin', 'guard_name' => 'web']);

        $permissions = Permission::where('guard_name', 'web')->whereIn('name', $posPermissions)->pluck('id')->all();
        $role->syncPermissions($permissions);

        foreach($users as $user)
        {
            $user->assignRole($role->id);
        }
    }
}
