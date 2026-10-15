<?php

use App\Models\Review;
use App\Models\Staff;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Livewire\Volt\Volt;

function superAdmin(): User
{
    Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder']);

    $user = User::factory()->create();
    $user->assignRole('superadmin');

    return $user;
}

test('superadmin can list users and open a store with staff and reviews', function () {
    $admin = superAdmin();
    $owner = User::factory()->create(['name' => 'Shop Owner']);
    $store = Store::create([
        'user_id' => $owner->id,
        'name' => 'Central Cafe',
        'qr_identifier' => (string) Str::uuid(),
    ]);
    Staff::create([
        'store_id' => $store->id,
        'name' => 'Aung Staff',
        'role' => 'Barista',
    ]);
    Review::create([
        'store_id' => $store->id,
        'rating_store' => 5,
        'comment' => 'Loved the tea',
    ]);

    $this->actingAs($admin)
        ->get(route('superadmin.users.index'))
        ->assertOk()
        ->assertSee('Shop Owner');

    $this->actingAs($admin)
        ->get(route('superadmin.stores.show', $store))
        ->assertOk()
        ->assertSee('Aung Staff')
        ->assertSee('Loved the tea');
});

test('a normal user cannot open the superadmin panel', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('superadmin.dashboard'))
        ->assertForbidden();
});

test('a deactivated user cannot log in', function () {
    $user = User::factory()->create([
        'is_active' => false,
    ]);

    Volt::test('pages.auth.login')
        ->set('form.email', $user->email)
        ->set('form.password', 'password')
        ->call('login')
        ->assertHasErrors(['form.email']);

    $this->assertGuest();
});

test('a deactivated store rejects a public review', function () {
    $owner = User::factory()->create();
    $store = Store::create([
        'user_id' => $owner->id,
        'name' => 'Closed Cafe',
        'qr_identifier' => (string) Str::uuid(),
        'is_active' => false,
    ]);

    $this->get(route('public.review', $store->slug))
        ->assertOk()
        ->assertSee('not accepting reviews');

    $this->post(route('public.submit-review', $store->slug), [
        'rating_store' => 5,
        'comment' => 'Should not save',
    ]);

    expect(Review::where('store_id', $store->id)->count())->toBe(0);
});

test('a superadmin cannot deactivate their own account', function () {
    $admin = superAdmin();

    $this->actingAs($admin)
        ->patch(route('superadmin.users.active', $admin))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($admin->fresh()->is_active)->toBeTrue();
});
