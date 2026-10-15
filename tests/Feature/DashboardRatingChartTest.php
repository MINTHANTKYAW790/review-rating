<?php

use App\Models\Review;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

function createStoreFor(User $owner, string $name, ?User $manager = null): Store
{
    return Store::create([
        'user_id' => $owner->id,
        'manager_id' => $manager?->id,
        'name' => $name,
        'qr_identifier' => (string) Str::uuid(),
    ]);
}

function seedRoles(): void
{
    Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
}

test('owner dashboard includes per-store star counts', function () {
    seedRoles();

    $owner = User::factory()->create();
    $owner->assignRole('admin');

    $firstStore = createStoreFor($owner, 'Sunrise Cafe');
    $secondStore = createStoreFor($owner, 'Moonlight Diner');

    Review::create(['store_id' => $firstStore->id, 'rating_store' => 5]);
    Review::create(['store_id' => $firstStore->id, 'rating_store' => 5]);
    Review::create(['store_id' => $firstStore->id, 'rating_store' => 4]);
    Review::create(['store_id' => $secondStore->id, 'rating_store' => 1]);

    $this->actingAs($owner)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Sunrise Cafe')
        ->assertSee('Moonlight Diner')
        ->assertSee(__('messages.ratings_by_star'))
        ->assertSee("data-counts='[2,1,0,0,0]'", false)
        ->assertSee("data-counts='[0,0,0,0,1]'", false);
});

test('manager dashboard shows only the assigned store star counts', function () {
    seedRoles();

    $owner = User::factory()->create();
    $owner->assignRole('admin');

    $manager = User::factory()->create();
    $manager->assignRole('manager');

    $assigned = createStoreFor($owner, 'Assigned Branch', $manager);
    $other = createStoreFor($owner, 'Other Branch');

    Review::create(['store_id' => $assigned->id, 'rating_store' => 3]);
    Review::create(['store_id' => $assigned->id, 'rating_store' => 3]);
    Review::create(['store_id' => $other->id, 'rating_store' => 5]);

    $this->actingAs($manager)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Assigned Branch')
        ->assertDontSee('Other Branch')
        ->assertSee("data-counts='[0,0,2,0,0]'", false);
});

test('a manager cannot see another store they do not manage', function () {
    seedRoles();

    $owner = User::factory()->create();
    $owner->assignRole('admin');

    $manager = User::factory()->create();
    $manager->assignRole('manager');
    $otherManager = User::factory()->create();
    $otherManager->assignRole('manager');

    $store = createStoreFor($owner, 'Private Branch', $manager);
    Review::create(['store_id' => $store->id, 'rating_store' => 5]);

    $this->actingAs($otherManager)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Private Branch')
        ->assertSee(__('messages.welcome_no_managed_stores'));

    $this->actingAs($otherManager)
        ->get(route('store.show', $store))
        ->assertForbidden();
});

test('unassigned manager sees an empty chart state', function () {
    seedRoles();

    $manager = User::factory()->create();
    $manager->assignRole('manager');

    $this->actingAs($manager)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee(__('messages.welcome_no_managed_stores'))
        ->assertDontSee(__('messages.create_first_store'));
});
