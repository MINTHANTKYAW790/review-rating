<?php

use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Str;

test('store owner can view their store edit page', function () {
    $owner = User::factory()->create();
    $store = Store::create([
        'user_id' => $owner->id,
        'name' => 'Owner Store',
        'qr_identifier' => (string) Str::uuid(),
    ]);

    $this->actingAs($owner)
        ->get(route('store.edit', $store))
        ->assertOk();
});

test('another user cannot edit a store they do not own', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $store = Store::create([
        'user_id' => $owner->id,
        'name' => 'Private Store',
        'qr_identifier' => (string) Str::uuid(),
    ]);

    $this->actingAs($intruder)
        ->get(route('store.edit', $store))
        ->assertForbidden();
});
