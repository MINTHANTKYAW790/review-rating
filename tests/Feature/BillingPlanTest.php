<?php

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Review;
use App\Models\Staff;
use App\Models\Store;
use App\Models\StoreCredit;
use App\Models\StoreSubscription;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

function seedBillingCatalog(): void
{
    Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder']);
    Artisan::call('db:seed', ['--class' => 'PlanSeeder']);
}

function billingOwner(): User
{
    seedBillingCatalog();

    $owner = User::factory()->create();
    $owner->assignRole('admin');

    return $owner;
}

function billingSuperadmin(): User
{
    seedBillingCatalog();

    $user = User::factory()->create();
    $user->assignRole('superadmin');

    return $user;
}

function billingStore(User $owner, string $name): Store
{
    return Store::create([
        'user_id' => $owner->id,
        'name' => $name,
        'qr_identifier' => (string) Str::uuid(),
        'is_active' => true,
    ]);
}

test('first store created over http receives the free plan', function () {
    $owner = billingOwner();

    $this->actingAs($owner)
        ->post(route('store.store'), [
            'name' => 'First Cafe',
        ])
        ->assertRedirect(route('store.index'));

    $store = Store::query()->where('user_id', $owner->id)->first();
    $subscription = $store->currentSubscription();

    expect($subscription)->not->toBeNull()
        ->and($subscription->plan->slug)->toBe(Plan::SLUG_FREE)
        ->and($subscription->ends_at)->toBeNull();
});

test('owner cannot create a second store without a store credit', function () {
    $owner = billingOwner();
    billingStore($owner, 'Only Cafe');

    $this->actingAs($owner)
        ->get(route('store.create'))
        ->assertRedirect(route('billing.add-store'));

    $this->actingAs($owner)
        ->post(route('store.store'), [
            'name' => 'Second Cafe',
        ])
        ->assertRedirect(route('billing.add-store'));

    expect(Store::query()->where('user_id', $owner->id)->count())->toBe(1);
});

test('billing index redirects to add-store', function () {
    $owner = billingOwner();

    $this->actingAs($owner)
        ->get(route('billing.index'))
        ->assertRedirect(route('billing.add-store'));
});

test('add-store page shows the add card without duration radios', function () {
    $owner = billingOwner();
    billingStore($owner, 'Shown Shop');

    $this->actingAs($owner)
        ->get(route('billing.add-store'))
        ->assertOk()
        ->assertSee(__('messages.add_one_store_card'))
        ->assertSee(__('messages.compare_free'))
        ->assertSee(__('messages.compare_pro'))
        ->assertSee(__('messages.compare_pro_reviews'))
        ->assertDontSee(__('messages.choose_duration'))
        ->assertDontSee('Store Pro 6 months');
});

test('extend page shows duration options and shop checkboxes', function () {
    $owner = billingOwner();
    billingStore($owner, 'Shown Shop');

    $this->actingAs($owner)
        ->get(route('billing.extend'))
        ->assertOk()
        ->assertSee(__('messages.extend_stores_card'))
        ->assertSee(__('messages.compare_free'))
        ->assertSee(__('messages.compare_pro_reviews'))
        ->assertSee('Shown Shop')
        ->assertSee('Store Pro 1 month')
        ->assertSee('Store Pro 3 months')
        ->assertSee('Store Pro 6 months')
        ->assertSee('Store Pro 9 months')
        ->assertSee('Store Pro 1 year');
});

test('payments page lists submitted invoices', function () {
    Storage::fake('public');
    $owner = billingOwner();

    $this->actingAs($owner)
        ->post(route('billing.store'), [
            'intent' => 'add_store',
            'plan_id' => Plan::pro()->id,
            'method' => Payment::METHOD_KBZ,
            'reference' => 'HIST-1',
            'screenshot' => UploadedFile::fake()->image('receipt.jpg'),
        ]);

    $payment = Payment::query()->where('user_id', $owner->id)->first();

    $this->actingAs($owner)
        ->get(route('billing.payments'))
        ->assertOk()
        ->assertSee((string) $payment->id)
        ->assertSee('5,000')
        ->assertSee('Pending');
});

test('add-store invoice is 5000 mmk for one new shop credit', function () {
    Storage::fake('public');
    $owner = billingOwner();

    $this->actingAs($owner)
        ->post(route('billing.store'), [
            'intent' => 'add_store',
            'plan_id' => Plan::pro()->id,
            'method' => Payment::METHOD_KBZ,
            'reference' => 'TXN-ADD',
            'screenshot' => UploadedFile::fake()->image('receipt.jpg'),
        ])
        ->assertRedirect();

    $payment = Payment::query()->where('user_id', $owner->id)->first();

    expect($payment->amount_mmk)->toBe(5000)
        ->and($payment->items)->toHaveCount(1)
        ->and($payment->items->first()->type)->toBe(\App\Models\PaymentItem::TYPE_NEW_STORE_SLOT)
        ->and($payment->items->first()->plan->slug)->toBe(Plan::SLUG_PRO);
});

test('extending two shops on the one-month plan totals 10000 mmk', function () {
    Storage::fake('public');
    $owner = billingOwner();
    $one = billingStore($owner, 'Shop One');
    $two = billingStore($owner, 'Shop Two');

    $this->actingAs($owner)
        ->post(route('billing.store'), [
            'intent' => 'extend',
            'plan_id' => Plan::pro()->id,
            'store_ids' => [$one->id, $two->id],
            'method' => Payment::METHOD_KBZ,
            'reference' => 'TXN-10000',
            'screenshot' => UploadedFile::fake()->image('receipt.jpg'),
        ])
        ->assertRedirect();

    $payment = Payment::query()->where('user_id', $owner->id)->first();

    expect($payment->amount_mmk)->toBe(10000)
        ->and($payment->status)->toBe(Payment::STATUS_PENDING)
        ->and($payment->items)->toHaveCount(2)
        ->and($payment->items->first()->duration_days)->toBe(30);
});

test('extending two shops on the six-month plan totals 60000 mmk', function () {
    Storage::fake('public');
    $owner = billingOwner();
    $one = billingStore($owner, 'Shop One');
    $two = billingStore($owner, 'Shop Two');
    $sixMonth = Plan::query()->where('slug', Plan::SLUG_PRO_6M)->first();

    $this->actingAs($owner)
        ->post(route('billing.store'), [
            'intent' => 'extend',
            'plan_id' => $sixMonth->id,
            'store_ids' => [$one->id, $two->id],
            'method' => Payment::METHOD_KBZ,
            'reference' => 'TXN-60000',
            'screenshot' => UploadedFile::fake()->image('receipt.jpg'),
        ])
        ->assertRedirect();

    $payment = Payment::query()->where('user_id', $owner->id)->first();

    expect($payment->amount_mmk)->toBe(60000)
        ->and($payment->items)->toHaveCount(2)
        ->and($payment->items->first()->duration_days)->toBe(205)
        ->and($payment->items->first()->plan->slug)->toBe(Plan::SLUG_PRO_6M);
});

test('three-month catalog plan is 40 days and 15000 mmk', function () {
    seedBillingCatalog();

    $plan = Plan::query()->where('slug', Plan::SLUG_PRO_3M)->first();

    expect($plan)->not->toBeNull()
        ->and($plan->duration_days)->toBe(40)
        ->and($plan->price_mmk)->toBe(15000);
});

test('approving an extend payment renews selected stores', function () {
    Storage::fake('public');
    $owner = billingOwner();
    $superadmin = User::factory()->create();
    $superadmin->assignRole('superadmin');
    $one = billingStore($owner, 'Harbor');
    $two = billingStore($owner, 'Dock');

    $this->actingAs($owner)
        ->post(route('billing.store'), [
            'intent' => 'extend',
            'plan_id' => Plan::pro()->id,
            'store_ids' => [$one->id, $two->id],
            'method' => Payment::METHOD_WAVE,
            'reference' => 'WAVE-EXTEND',
            'screenshot' => UploadedFile::fake()->image('receipt.jpg'),
        ]);

    $payment = Payment::query()->first();

    $this->actingAs($superadmin)
        ->post(route('superadmin.payments.approve', $payment))
        ->assertRedirect(route('superadmin.payments.show', $payment));

    expect($one->fresh()->currentSubscription()?->plan->slug)->toBe(Plan::SLUG_PRO)
        ->and($two->fresh()->currentSubscription()?->plan->slug)->toBe(Plan::SLUG_PRO)
        ->and($one->fresh()->currentSubscription()?->ends_at?->isFuture())->toBeTrue()
        ->and(StoreCredit::query()->unused()->where('user_id', $owner->id)->count())->toBe(0);
});

test('approving an add-store payment grants a store credit', function () {
    Storage::fake('public');
    $owner = billingOwner();
    $superadmin = User::factory()->create();
    $superadmin->assignRole('superadmin');
    billingStore($owner, 'First Shop');

    $this->actingAs($owner)
        ->post(route('billing.store'), [
            'intent' => 'add_store',
            'plan_id' => Plan::pro()->id,
            'method' => Payment::METHOD_WAVE,
            'reference' => 'WAVE-ADD',
            'screenshot' => UploadedFile::fake()->image('receipt.jpg'),
        ]);

    $payment = Payment::query()->first();

    $this->actingAs($superadmin)
        ->post(route('superadmin.payments.approve', $payment))
        ->assertRedirect();

    expect(StoreCredit::query()->unused()->where('user_id', $owner->id)->count())->toBe(1);

    $this->actingAs($owner)
        ->post(route('store.store'), [
            'name' => 'New Branch',
        ])
        ->assertRedirect(route('store.index'));

    $branch = Store::query()->where('name', 'New Branch')->first();

    expect($branch->currentSubscription()?->plan->slug)->toBe(Plan::SLUG_PRO)
        ->and(StoreCredit::query()->unused()->where('user_id', $owner->id)->count())->toBe(0);
});

test('rejecting a payment does not change subscriptions', function () {
    Storage::fake('public');
    $owner = billingOwner();
    $superadmin = User::factory()->create();
    $superadmin->assignRole('superadmin');
    $store = billingStore($owner, 'Keep Free');

    app(BillingService::class)->attachFreeSubscription($store);

    $this->actingAs($owner)
        ->post(route('billing.store'), [
            'intent' => 'extend',
            'plan_id' => Plan::pro()->id,
            'store_ids' => [$store->id],
            'method' => Payment::METHOD_KBZ,
            'reference' => 'TXN-REJECT',
            'screenshot' => UploadedFile::fake()->image('receipt.jpg'),
        ]);

    $payment = Payment::query()->first();

    $this->actingAs($superadmin)
        ->post(route('superadmin.payments.reject', $payment), [
            'admin_note' => 'Unclear screenshot',
        ])
        ->assertRedirect();

    expect($payment->fresh()->status)->toBe(Payment::STATUS_REJECTED)
        ->and($store->fresh()->currentSubscription()?->plan->slug)->toBe(Plan::SLUG_FREE)
        ->and(StoreCredit::query()->count())->toBe(0);
});

test('a second pending invoice is blocked', function () {
    Storage::fake('public');
    $owner = billingOwner();
    $store = billingStore($owner, 'Solo');

    $this->actingAs($owner)
        ->post(route('billing.store'), [
            'intent' => 'extend',
            'plan_id' => Plan::pro()->id,
            'store_ids' => [$store->id],
            'method' => Payment::METHOD_KBZ,
            'reference' => 'FIRST',
            'screenshot' => UploadedFile::fake()->image('receipt.jpg'),
        ])
        ->assertRedirect();

    $this->actingAs($owner)
        ->post(route('billing.store'), [
            'intent' => 'extend',
            'plan_id' => Plan::pro()->id,
            'store_ids' => [$store->id],
            'method' => Payment::METHOD_KBZ,
            'reference' => 'SECOND',
            'screenshot' => UploadedFile::fake()->image('receipt.jpg'),
        ])
        ->assertSessionHasErrors('payment');

    expect(Payment::query()->count())->toBe(1);
});

test('free plan blocks a fourth staff member', function () {
    $owner = billingOwner();
    $store = billingStore($owner, 'Tiny Shop');
    app(BillingService::class)->attachFreeSubscription($store);

    foreach (['One', 'Two', 'Three'] as $name) {
        Staff::create(['store_id' => $store->id, 'name' => $name]);
    }

    $this->actingAs($owner)
        ->post(route('staff.store'), [
            'name' => 'Four',
            'store_id' => $store->id,
        ])
        ->assertSessionHasErrors('store_id');

    expect(Staff::query()->where('store_id', $store->id)->count())->toBe(3);
});

test('free plan blocks reviews after the lifetime cap', function () {
    $owner = billingOwner();
    $store = billingStore($owner, 'Capped Cafe');
    app(BillingService::class)->attachFreeSubscription($store);

    for ($i = 0; $i < 30; $i++) {
        Review::create([
            'store_id' => $store->id,
            'rating_store' => 5,
            'comment' => 'Review '.$i,
        ]);
    }

    $this->get(route('public.review', $store->slug))
        ->assertOk()
        ->assertSee('not accepting reviews');

    $this->post(route('public.submit-review', $store->slug), [
        'rating_store' => 4,
        'comment' => 'Should fail',
    ]);

    expect(Review::query()->where('store_id', $store->id)->count())->toBe(30);
});

test('expired paid extra shop is deactivated and the first shop falls back to free', function () {
    $owner = billingOwner();
    $first = billingStore($owner, 'Original');
    $extra = billingStore($owner, 'Branch');
    $pro = Plan::pro();

    StoreSubscription::create([
        'store_id' => $first->id,
        'plan_id' => $pro->id,
        'status' => StoreSubscription::STATUS_ACTIVE,
        'starts_at' => now()->subDays(40),
        'ends_at' => now()->subMinute(),
        'price_mmk' => 5000,
    ]);
    StoreSubscription::create([
        'store_id' => $extra->id,
        'plan_id' => $pro->id,
        'status' => StoreSubscription::STATUS_ACTIVE,
        'starts_at' => now()->subDays(40),
        'ends_at' => now()->subMinute(),
        'price_mmk' => 5000,
    ]);

    Artisan::call('subscriptions:expire');

    expect($first->fresh()->is_active)->toBeTrue()
        ->and($first->fresh()->currentSubscription()?->plan->slug)->toBe(Plan::SLUG_FREE)
        ->and($extra->fresh()->is_active)->toBeFalse()
        ->and($extra->fresh()->currentSubscription())->toBeNull();
});

test('superadmin can open plans payments and subscriptions screens', function () {
    $admin = billingSuperadmin();

    $this->actingAs($admin)
        ->get(route('superadmin.plans.index'))
        ->assertOk()
        ->assertSee('Store Pro');

    $this->actingAs($admin)
        ->get(route('superadmin.payments.index'))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('superadmin.subscriptions.index'))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('superadmin.billing-settings.edit'))
        ->assertOk();
});
