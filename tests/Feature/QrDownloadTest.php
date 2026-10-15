<?php

use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Str;

test('store owner can download a styled qr svg', function () {
    $owner = User::factory()->create();
    $store = Store::create([
        'user_id' => $owner->id,
        'name' => 'QR Store',
        'qr_identifier' => (string) Str::uuid(),
    ]);

    $response = $this->actingAs($owner)
        ->get(route('qr.download.template', [$store, 'classic']));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('image/svg+xml');
    expect($response->getContent())->toContain('<svg');
});

test('non owner cannot download store qr', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $store = Store::create([
        'user_id' => $owner->id,
        'name' => 'Locked QR Store',
        'qr_identifier' => (string) Str::uuid(),
    ]);

    $this->actingAs($other)
        ->get(route('qr.download.template', [$store, 'classic']))
        ->assertForbidden();
});
