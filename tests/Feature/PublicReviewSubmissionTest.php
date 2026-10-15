<?php

use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Str;

test('public review page renders stepped form sections', function () {
    $user = User::factory()->create();
    $store = Store::create([
        'user_id' => $user->id,
        'name' => 'Stepper Cafe',
        'qr_identifier' => (string) Str::uuid(),
    ]);

    $this->get(route('public.review', $store->slug))
        ->assertOk()
        ->assertSee('review-step', false)
        ->assertSee('step-btn-next', false)
        ->assertSee(__('messages.review_next'));
});

test('public review can be submitted for a store', function () {
    $user = User::factory()->create();
    $store = Store::create([
        'user_id' => $user->id,
        'name' => 'Public Cafe',
        'qr_identifier' => (string) Str::uuid(),
    ]);

    $response = $this->post(route('public.submit-review', $store->slug), [
        'rating_store' => 5,
        'comment' => 'Great place',
    ]);

    $response->assertRedirect(route('public.review', $store->slug));
    $this->assertDatabaseHas('reviews', [
        'store_id' => $store->id,
        'rating_store' => 5,
        'comment' => 'Great place',
    ]);
});

test('public review rejects a second submission from the same ip on the same day', function () {
    $user = User::factory()->create();
    $store = Store::create([
        'user_id' => $user->id,
        'name' => 'Dedupe Cafe',
        'qr_identifier' => (string) Str::uuid(),
    ]);

    $this->post(route('public.submit-review', $store->slug), [
        'rating_store' => 4,
        'comment' => 'First review',
    ])->assertRedirect();

    $this->post(route('public.submit-review', $store->slug), [
        'rating_store' => 3,
        'comment' => 'Second review',
    ])->assertSessionHasErrors('review');

    expect(\App\Models\Review::where('store_id', $store->id)->count())->toBe(1);
});
