<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

test('role and permission seeder creates the expected default roles and grants the correct permissions', function () {
    Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder']);

    expect(Role::findByName('superadmin'))->not->toBeNull()
        ->and(Role::findByName('admin'))->not->toBeNull()
        ->and(Role::findByName('manager'))->not->toBeNull()
        ->and(Role::findByName('staff'))->not->toBeNull()
        ->and(Role::findByName('customer'))->not->toBeNull();

    expect(Permission::findByName('view_roles'))->not->toBeNull()
        ->and(Permission::findByName('view_users'))->not->toBeNull()
        ->and(Permission::findByName('view_reviews'))->not->toBeNull()
        ->and(Permission::findByName('view_activity_log'))->not->toBeNull();

    $superadmin = Role::findByName('superadmin');
    $admin = Role::findByName('admin');
    $manager = Role::findByName('manager');
    $staff = Role::findByName('staff');
    $customer = Role::findByName('customer');

    expect($superadmin->hasPermissionTo('manage_system_settings'))->toBeTrue()
        ->and($superadmin->hasPermissionTo('view_activity_log'))->toBeTrue()
        ->and($admin->hasPermissionTo('edit_stores'))->toBeTrue()
        ->and($admin->hasPermissionTo('view_activity_log'))->toBeFalse()
        ->and($admin->hasPermissionTo('view_roles'))->toBeFalse()
        ->and($manager->hasPermissionTo('view_dashboard'))->toBeTrue()
        ->and($manager->hasPermissionTo('view_reviews'))->toBeTrue()
        ->and($manager->hasPermissionTo('create_stores'))->toBeFalse()
        ->and($staff->hasPermissionTo('reply_reviews'))->toBeTrue()
        ->and($customer->hasPermissionTo('view_staff'))->toBeTrue();
});

test('admin users cannot open the roles screen', function () {
    Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder']);

    $adminUser = User::factory()->create();
    $adminUser->assignRole('admin');

    $this->actingAs($adminUser)
        ->get(route('roles.index'))
        ->assertForbidden();

    expect($adminUser->hasRole('superadmin'))->toBeFalse()
        ->and($adminUser->can('view_roles'))->toBeFalse();
});
