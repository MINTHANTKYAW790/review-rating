<?php

use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

function seedManagerRoles(): void
{
    Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
}

function ownedStore(User $owner, string $name): Store
{
    return Store::create([
        'user_id' => $owner->id,
        'name' => $name,
        'qr_identifier' => (string) Str::uuid(),
    ]);
}

test('admin can create a manager and assign a store on the same form', function () {
    seedManagerRoles();

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $store = ownedStore($admin, 'Harbor Cafe');

    $this->actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'Pat Manager',
            'email' => 'pat.manager@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'roles' => ['manager'],
            'store_id' => $store->id,
        ])
        ->assertRedirect(route('users.index'));

    $manager = User::where('email', 'pat.manager@example.com')->first();

    expect($manager)->not->toBeNull()
        ->and($manager->hasRole('manager'))->toBeTrue()
        ->and($store->fresh()->manager_id)->toBe($manager->id);

    $this->actingAs($admin)
        ->get(route('users.show', $manager))
        ->assertOk()
        ->assertSee('Harbor Cafe');

    $this->actingAs($manager)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Harbor Cafe');
});

test('admin cannot assign a store they do not own when creating a manager', function () {
    seedManagerRoles();

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $otherOwner = User::factory()->create();
    $otherOwner->assignRole('admin');
    $foreignStore = ownedStore($otherOwner, 'Foreign Shop');

    $this->actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'Pat Manager',
            'email' => 'pat.manager@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'roles' => ['manager'],
            'store_id' => $foreignStore->id,
        ])
        ->assertSessionHasErrors('store_id');

    expect($foreignStore->fresh()->manager_id)->toBeNull();
});

test('removing the manager role unassigns the store', function () {
    seedManagerRoles();

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $manager = User::factory()->create(['created_by' => $admin->id]);
    $manager->assignRole('manager');
    $store = ownedStore($admin, 'River Shop');
    $store->update(['manager_id' => $manager->id]);

    $this->actingAs($admin)
        ->put(route('users.update', $manager), [
            'name' => $manager->name,
            'email' => $manager->email,
            'roles' => [],
        ])
        ->assertRedirect(route('users.index'));

    expect($manager->fresh()->hasRole('manager'))->toBeFalse()
        ->and($store->fresh()->manager_id)->toBeNull();
});

test('store edit can still assign a manager independently', function () {
    seedManagerRoles();

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $manager = User::factory()->create(['created_by' => $admin->id]);
    $manager->assignRole('manager');
    $store = ownedStore($admin, 'Independent Shop');

    $this->actingAs($admin)
        ->put(route('store.update', $store), [
            'name' => $store->name,
            'manager_id' => $manager->id,
        ])
        ->assertRedirect(route('store.index'));

    expect($store->fresh()->manager_id)->toBe($manager->id);
});

test('user create page includes the store assignment field', function () {
    seedManagerRoles();

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    ownedStore($admin, 'Visible Shop');

    $this->actingAs($admin)
        ->get(route('users.create'))
        ->assertOk()
        ->assertSee(__('messages.assign_store'))
        ->assertSee('Visible Shop')
        ->assertSee('value="manager"', false)
        ->assertDontSee('value="staff"', false)
        ->assertDontSee('value="customer"', false)
        ->assertDontSee('value="admin"', false);
});

test('assigning a manager to a store replaces the previous manager', function () {
    seedManagerRoles();

    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $first = User::factory()->create();
    $first->assignRole('manager');
    $second = User::factory()->create();
    $second->assignRole('manager');
    $store = ownedStore($admin, 'Single Manager Shop');
    $store->update(['manager_id' => $first->id]);

    $this->actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'New Manager',
            'email' => 'new.manager@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'roles' => ['manager'],
            'store_id' => $store->id,
        ])
        ->assertRedirect(route('users.index'));

    $newManager = User::where('email', 'new.manager@example.com')->first();

    expect($store->fresh()->manager_id)->toBe($newManager->id)
        ->and($first->fresh()->managedStores)->toHaveCount(0);
});

test('admin cannot assign the staff or customer login roles', function () {
    seedManagerRoles();

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'Guest User',
            'email' => 'guest.user@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'roles' => ['staff'],
        ])
        ->assertSessionHasErrors('roles.0');
});
