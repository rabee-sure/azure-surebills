<?php

namespace Database\Seeders;

use App\Models\Admin;
use Exception;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\Role;
use App\Models\User;

class CreateAdminSuperAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->migrateAdminsFromUserTableToAdminsTable();
        $role = $this->createSuperAdminRole();
        $admins = Admin::whereDoesntHave('roles')->get();

        foreach($admins as $admin)
        {
            $admin->assignRole($role->id);
        }
    }

    private function migrateAdminsFromUserTableToAdminsTable()
    {
        foreach(explode(',', env('NOVA_ALLOWED_ADMINS')) as $adminEmail)
        {
            if(!Admin::where('email', $adminEmail)->first())
            {
                $admin = User::where('email', $adminEmail)->first();
                if($admin)
                {
                    Admin::create([
                        'name' => $admin->name,
                        'email' => $adminEmail,
                        'password' => $admin->password,
                        'mobile' => $admin->mobile,
                    ]);
                }
            }
        }
    }

    private function createSuperAdminRole()
    {
        $role = Role::firstOrCreate(['name' => 'super admin', 'guard_name' => 'admins'], ['name' => 'super admin', 'guard_name' => 'admins']);
        $permissions = Permission::where('guard_name', 'admins')->pluck('id')->all();
        $role->syncPermissions($permissions);
        return $role;
    }
}
