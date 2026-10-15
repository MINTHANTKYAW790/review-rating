<?php

use App\Models\Plan;
use App\Models\Review;
use App\Models\Store;
use App\Models\StoreSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

function seedHeaderCueCatalog(): void
{
    Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
    Artisan::call('db:seed', ['--class' => 'PlanSeeder']);
}

function headerCueOwner(): User
{
    seedHeaderCueCatalog();

    $owner = User::factory()->create();
    $owner->assignRole('admin');

    return $owner;
}

function headerCueStore(User $owner, string $name): Store
{
    return Store::create([
        'user_id' => $owner->id,
        'name' => $name,
        'qr_identifier' => (string) Str::uuid(),
        'is_active' => true,
    ]);
}

test('free store header cue shows review usage and links to extend', function () {
    $owner = headerCueOwner();
    $store = headerCueStore($owner, 'Quota Cafe');

    foreach (range(1, 3) as $i) {
        Review::create([
            'store_id' => $store->id,
            'rating_store' => 5,
        ]);
    }

    $this->actingAs($owner)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee(__('messages.header_reviews_used', ['used' => 3, 'max' => 30]), false)
        ->assertSee(route('billing.extend'), false)
        ->assertDontSee(__('messages.header_days_left', ['days' => 38]));
});

test('pro store header cue shows days left', function () {
    $owner = headerCueOwner();
    $store = headerCueStore($owner, 'Pro Cafe');
    $pro = Plan::pro();

    StoreSubscription::create([
        'store_id' => $store->id,
        'plan_id' => $pro->id,
        'status' => StoreSubscription::STATUS_ACTIVE,
        'starts_at' => now(),
        'ends_at' => now()->addDays(38)->addHour(),
        'price_mmk' => 5000,
    ]);

    $this->actingAs($owner)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee(__('messages.header_days_left', ['days' => 38]), false)
        ->assertSee(route('billing.extend'), false)
        ->assertDontSee(__('messages.header_reviews_used', ['used' => 0, 'max' => 30]), false);
});

test('superadmin does not see the header plan cue', function () {
    seedHeaderCueCatalog();

    $superadmin = User::factory()->create();
    $superadmin->assignRole('superadmin');

    $this->actingAs($superadmin)
        ->get(route('superadmin.dashboard'))
        ->assertOk()
        ->assertDontSee('header-plan-cue', false)
        ->assertDontSee(__('messages.header_reviews_used', ['used' => 0, 'max' => 30]), false);
});
