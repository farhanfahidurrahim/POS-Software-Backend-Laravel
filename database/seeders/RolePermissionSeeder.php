<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Role::create([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Permission::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $permissions_data = [
            //admin permission start
            [
                'module_name' => 'Dashboard',
                'guard_name' => 'web',
                'permissions' => [
                    'dashboard.show'
                ]
            ],
            [
                'module_name' => 'Category-Manage',
                'guard_name' => 'web',
                'permissions' => [
                    'category.show',
                    'category.add',
                    'category.edit',
                    'category.delete',
                    'category.import'
                ]
            ],
            [
                'module_name' => 'Seller-Manage',
                'guard_name' => 'web',
                'permissions' => [
                    'seller.show'
                ]
            ],
            [
                'module_name' => 'Access-Control-Manage',
                'guard_name' => 'web',
                'permissions' => [
                    'user.show',
                    'user.add',
                    'user.edit',
                    'user.delete',
                    'user.restore',
                    'user.delete-permanently',
                    'role.show',
                    'role.add',
                    'role.edit',
                ]
            ],
        ];
        foreach ($permissions_data as $permission_data) {
            $module_name = $permission_data['module_name'];
            $guard_name = $permission_data['guard_name'];
            foreach ($permission_data['permissions'] as $permission) {
                Permission::insert(['name' => $permission, 'guard_name' => $guard_name, 'module_name' => $module_name]);
            }
        }


    }
}
