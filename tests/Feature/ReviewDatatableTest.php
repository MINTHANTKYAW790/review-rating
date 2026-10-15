<?php

use App\Models\Review;
use App\Models\Staff;
use App\Models\Store;
use App\Models\User;

test('review index returns datatables json payload for ajax requests', function () {
    $user = User::factory()->create();
    $store = Store::create([
        'user_id' => $user->id,
        'name' => 'Cafe Aurora',
        'address' => '123 Main St',
        'phone' => '0123456789',
        'email' => 'cafe@example.com',
        'qr_identifier' => 'cafe-aurora-'.uniqid(),
    ]);
    $staff = Staff::create([
        'store_id' => $store->id,
        'name' => 'Dana',
        'role' => 'Barista',
        'rating' => 4.8,
    ]);

    Review::create([
        'store_id' => $store->id,
        'staff_id' => $staff->id,
        'rating_store' => 5,
        'rating_staff' => 4,
        'comment' => 'Excellent service and coffee.',
        'customer_phone' => '0123456789',
        'contact_requested' => true,
    ]);

    $this->actingAs($user);

    $response = $this->withHeader('X-Requested-With', 'XMLHttpRequest')
        ->getJson('/review?draw=1&start=0&length=10');

    $response
        ->assertOk()
        ->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data' => [
                '*' => [
                    'shop_name',
                    'shop_star',
                    'staff_name',
                    'staff_star',
                    'customer_phone',
                    'attention',
                    'review_text',
                    'remark_preview',
                    'reviewed_at',
                    'action',
                ],
            ],
        ]);

    expect($response->json('data.0.shop_name'))->toBe('Cafe Aurora');
});
