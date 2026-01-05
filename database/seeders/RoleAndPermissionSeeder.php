<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view products',
            'create products',
            'edit products',
            'delete products',
            'view orders',
            'create orders',
            'edit orders',
            'delete orders',
            'view reports',
            'generate reports',
            'manage settings',
            'access dashboard',
            'manage inventory',
            'process returns',
            'manage suppliers',
            'manage customers',
            'view analytics',
            'export data',
            'import data',
            'manage discounts',
            'manage promotions',
            'view audit logs',
            'manage roles',
            'manage permissions',
            'view sales',
            'manage purchases',
            'create sales',
            'edit sales',
            'delete sales',
            'create purchases',
            'edit purchases',
            'delete purchases',
            'manage taxes',
            'view cashier',
            'manage cashier',

        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $roles = [
            'admin' => $permissions,
            'manager' => [
                'view users',
                'view products',
                'create products',
                'edit products',
                'view orders',
                'edit orders',
            ],
            'pharmacist' => [
                'view products',
                'view sales',
                'create sales',
                'view orders',
                'create orders',
                'process returns',
            ],
            'cashier' => [
                'view products',
                'view orders',
                'view sales',
                'manage cashier',
            ],
            'inventory_clerk' => [
                'view products',
                'manage inventory',
                'manage suppliers',
                'manage purchases',
            ],
            'auditor' => [
                'view audit logs',
                'view reports',
                'view analytics',
            ],
            'staff' => [
                'view products',
                'view orders',
                'create orders',
            ],
        ];

        foreach ($roles as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($perms);
        }

        $user = User::firstWhere('email', 'test@example.com');
        if ($user) {
            $user->assignRole('admin');
        }
    }
}
