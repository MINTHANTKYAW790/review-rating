<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'System' => [
                'view_dashboard',
                'manage_system_settings',
                'view_activity_log',
            ],
            'Roles' => [
                'view_roles',
                'create_roles',
                'edit_roles',
                'delete_roles',
            ],
            'Users' => [
                'view_users',
                'create_users',
                'edit_users',
                'delete_users',
            ],
            'Stores' => [
                'view_stores',
                'create_stores',
                'edit_stores',
                'delete_stores',
            ],
            'Staff' => [
                'view_staff',
                'create_staff',
                'edit_staff',
                'delete_staff',
            ],
            'Reviews' => [
                'view_reviews',
                'reply_reviews',
            ],
            'Billing' => [
                'manage_plans',
                'manage_subscriptions',
                'review_payments',
                'buy_plans',
            ],
        ];

        foreach ($permissions as $group => $names) {
            foreach ($names as $name) {
                Permission::firstOrCreate([
                    'name' => $name,
                    'guard_name' => 'web',
                ]);
            }
        }

        $roles = ['superadmin', 'admin', 'manager', 'staff', 'customer'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }

        $superadmin = Role::findByName('superadmin', 'web');
        $admin = Role::findByName('admin', 'web');
        $manager = Role::findByName('manager', 'web');
        $staff = Role::findByName('staff', 'web');
        $customer = Role::findByName('customer', 'web');

        $allPermissions = Permission::pluck('name')->toArray();
        $superadmin->syncPermissions($allPermissions);

        $admin->syncPermissions([
            'view_dashboard',
            'manage_system_settings',
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_stores',
            'create_stores',
            'edit_stores',
            'delete_stores',
            'view_staff',
            'create_staff',
            'edit_staff',
            'delete_staff',
            'view_reviews',
            'buy_plans',
        ]);

        $manager->syncPermissions([
            'view_dashboard',
            'view_stores',
            'view_staff',
            'view_reviews',
        ]);

        $staff->syncPermissions([
            'view_staff',
            'reply_reviews',
        ]);

        $customer->syncPermissions([
            'view_stores',
            'view_staff',
        ]);
    }
}
