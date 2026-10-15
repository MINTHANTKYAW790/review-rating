<?php

use App\Models\Review;
use App\Models\Staff;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Str;

function makeStoreWithOwner(?User $owner = null): array
{
    $owner ??= User::factory()->create();
    $store = Store::create([
        'user_id' => $owner->id,
        'name' => 'Triage Cafe '.Str::random(5),
        'qr_identifier' => (string) Str::uuid(),
    ]);

    return [$owner, $store];
}

test('public reviews start in the inbox', function () {
    [, $store] = makeStoreWithOwner();

    $this->post(route('public.submit-review', $store->slug), [
        'rating_store' => 2,
        'comment' => 'Please call me',
        'customer_phone' => '0912345678',
        'contact_requested' => '1',
    ])->assertRedirect();

    $this->assertDatabaseHas('reviews', [
        'store_id' => $store->id,
        'contact_requested' => 1,
        'follow_up_status' => Review::STATUS_INBOX,
    ]);
});

test('opening a review marks it read but keeps it in the inbox', function () {
    [$owner, $store] = makeStoreWithOwner();
    $review = Review::create([
        'store_id' => $store->id,
        'rating_store' => 4,
        'comment' => 'Nice',
        'follow_up_status' => Review::STATUS_INBOX,
    ]);

    expect($review->read_at)->toBeNull();

    $this->actingAs($owner)
        ->get(route('review.show', $review))
        ->assertOk();

    $review->refresh();

    expect($review->read_at)->not->toBeNull()
        ->and($review->follow_up_status)->toBe(Review::STATUS_INBOX);
});

test('owner can star a review and save a remark', function () {
    [$owner, $store] = makeStoreWithOwner();
    $review = Review::create([
        'store_id' => $store->id,
        'rating_store' => 1,
        'comment' => 'Slow service',
        'follow_up_status' => Review::STATUS_INBOX,
    ]);

    $this->actingAs($owner)
        ->post(route('review.star', $review))
        ->assertRedirect(route('review.show', $review));

    $this->actingAs($owner)
        ->patch(route('review.triage', $review), [
            'admin_remark' => 'Call kitchen lead',
        ])
        ->assertRedirect(route('review.show', $review));

    $review->refresh();

    expect($review->is_important)->toBeTrue()
        ->and($review->admin_remark)->toBe('Call kitchen lead')
        ->and($review->remarked_by)->toBe($owner->id)
        ->and($review->remarked_at)->not->toBeNull();
});

test('assigned manager can mark a review done', function () {
    [$owner, $store] = makeStoreWithOwner();
    $manager = User::factory()->create();
    $store->update(['manager_id' => $manager->id]);

    $review = Review::create([
        'store_id' => $store->id,
        'rating_store' => 3,
        'comment' => 'OK',
        'follow_up_status' => Review::STATUS_INBOX,
    ]);

    $this->actingAs($manager)
        ->post(route('review.done', $review))
        ->assertRedirect(route('review.show', $review));

    expect($review->fresh()->follow_up_status)->toBe(Review::STATUS_DONE);

    $this->actingAs($manager)
        ->post(route('review.reopen', $review))
        ->assertRedirect(route('review.show', $review));

    expect($review->fresh()->follow_up_status)->toBe(Review::STATUS_INBOX);
});

test('outsider cannot update review triage', function () {
    [, $store] = makeStoreWithOwner();
    $outsider = User::factory()->create();
    $review = Review::create([
        'store_id' => $store->id,
        'rating_store' => 5,
        'comment' => 'Great',
    ]);

    $this->actingAs($outsider)
        ->patch(route('review.triage', $review), [
            'admin_remark' => 'Nope',
        ])
        ->assertForbidden();
});

test('review index queue chips filter inbox starred and wants contact', function () {
    [$owner, $store] = makeStoreWithOwner();
    $staff = Staff::create([
        'store_id' => $store->id,
        'name' => 'Dana',
        'role' => 'Barista',
    ]);

    Review::create([
        'store_id' => $store->id,
        'staff_id' => $staff->id,
        'rating_store' => 5,
        'rating_staff' => 4,
        'comment' => 'Starred inbox',
        'is_important' => true,
        'follow_up_status' => Review::STATUS_INBOX,
        'contact_requested' => false,
    ]);

    Review::create([
        'store_id' => $store->id,
        'staff_id' => $staff->id,
        'rating_store' => 2,
        'rating_staff' => 2,
        'comment' => 'Please call',
        'follow_up_status' => Review::STATUS_INBOX,
        'contact_requested' => true,
    ]);

    Review::create([
        'store_id' => $store->id,
        'staff_id' => $staff->id,
        'rating_store' => 4,
        'rating_staff' => 4,
        'comment' => 'Already handled',
        'read_at' => now(),
        'follow_up_status' => Review::STATUS_DONE,
    ]);

    $this->actingAs($owner);

    $inbox = $this->withHeader('X-Requested-With', 'XMLHttpRequest')
        ->getJson('/review?draw=1&start=0&length=10&queue=inbox');
    $inbox->assertOk();
    expect($inbox->json('recordsFiltered'))->toBe(2);

    $starred = $this->withHeader('X-Requested-With', 'XMLHttpRequest')
        ->getJson('/review?draw=1&start=0&length=10&queue=starred');
    $starred->assertOk();
    expect($starred->json('recordsFiltered'))->toBe(1)
        ->and($starred->json('data.0.review_text'))->toContain('Starred inbox');

    $contact = $this->withHeader('X-Requested-With', 'XMLHttpRequest')
        ->getJson('/review?draw=1&start=0&length=10&queue=wants_contact');
    $contact->assertOk();
    expect($contact->json('recordsFiltered'))->toBe(1)
        ->and($contact->json('data.0.review_text'))->toContain('Please call');

    $done = $this->withHeader('X-Requested-With', 'XMLHttpRequest')
        ->getJson('/review?draw=1&start=0&length=10&queue=done');
    $done->assertOk();
    expect($done->json('recordsFiltered'))->toBe(1);

    $all = $this->withHeader('X-Requested-With', 'XMLHttpRequest')
        ->getJson('/review?draw=1&start=0&length=10&queue=all');
    $all->assertOk();
    expect($all->json('recordsFiltered'))->toBe(3);
});
