<?php

use App\Models\Review;
use App\Models\Staff;
use App\Models\Store;
use App\Models\StoreNote;
use App\Models\User;
use Illuminate\Support\Str;

function makeOwnedStore(?User $owner = null): array
{
    $owner ??= User::factory()->create();
    $store = Store::create([
        'user_id' => $owner->id,
        'name' => 'Notes Cafe '.Str::random(5),
        'qr_identifier' => (string) Str::uuid(),
    ]);

    return [$owner, $store];
}

test('owner can create a store note', function () {
    [$owner, $store] = makeOwnedStore();

    $this->actingAs($owner)
        ->post(route('notes.store'), [
            'store_id' => $store->id,
            'title' => 'Train greeting',
            'body' => 'Remind staff to greet within 10 seconds.',
            'category' => 'staff',
            'status' => StoreNote::STATUS_OPEN,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('store_notes', [
        'store_id' => $store->id,
        'user_id' => $owner->id,
        'title' => 'Train greeting',
        'status' => StoreNote::STATUS_OPEN,
    ]);
});

test('create from review prefills store staff and review', function () {
    [$owner, $store] = makeOwnedStore();
    $staff = Staff::create([
        'store_id' => $store->id,
        'name' => 'Lin',
        'role' => 'Cashier',
    ]);
    $review = Review::create([
        'store_id' => $store->id,
        'staff_id' => $staff->id,
        'rating_store' => 2,
        'comment' => 'Long wait',
    ]);

    $this->actingAs($owner)
        ->get(route('notes.create', [
            'review_id' => $review->id,
            'store_id' => $store->id,
            'staff_id' => $staff->id,
        ]))
        ->assertOk()
        ->assertSee('name="review_id"', false)
        ->assertSee((string) $review->id)
        ->assertSee('disabled', false)
        ->assertSee('name="store_id"', false)
        ->assertSee('value="'.$store->id.'"', false)
        ->assertSee('selected', false);
});

test('free note create keeps the shop dropdown enabled', function () {
    [$owner, $store] = makeOwnedStore();

    $html = $this->actingAs($owner)
        ->get(route('notes.create'))
        ->assertOk()
        ->assertDontSee('name="review_id"', false)
        ->getContent();

    expect($html)
        ->toContain('id="store_id"')
        ->toContain('name="store_id"')
        ->toContain((string) $store->id)
        ->not->toContain('id="store_id" class="form-control" required disabled');
});

test('note create form tags staff options by store for client-side filtering', function () {
    [$owner, $store] = makeOwnedStore();
    $staff = Staff::create([
        'store_id' => $store->id,
        'name' => 'Lin',
        'role' => 'Cashier',
    ]);

    $this->actingAs($owner)
        ->get(route('notes.create', ['store_id' => $store->id]))
        ->assertOk()
        ->assertSee('js-staff-by-store', false)
        ->assertSee('data-store="'.$store->id.'"', false)
        ->assertSee('rebuildStaffOptions', false)
        ->assertSee($staff->name);
});

test('notes index tags staff filter options by store', function () {
    [$owner, $store] = makeOwnedStore();
    $staff = Staff::create([
        'store_id' => $store->id,
        'name' => 'Lin',
        'role' => 'Cashier',
    ]);

    $this->actingAs($owner)
        ->get(route('notes.index'))
        ->assertOk()
        ->assertSee('js-staff-by-store', false)
        ->assertSee('data-store="'.$store->id.'"', false)
        ->assertSee('data-submit-on-store-change="1"', false)
        ->assertSee($staff->name);
});

test('manager can manage notes for assigned store', function () {
    [$owner, $store] = makeOwnedStore();
    $manager = User::factory()->create();
    $store->update(['manager_id' => $manager->id]);

    $this->actingAs($manager)
        ->post(route('notes.store'), [
            'store_id' => $store->id,
            'title' => 'Ops idea',
            'body' => 'Add a queue ticket display.',
            'category' => 'ops',
            'status' => StoreNote::STATUS_OPEN,
        ])
        ->assertRedirect();

    $note = StoreNote::query()->where('title', 'Ops idea')->first();
    expect($note)->not->toBeNull();

    $this->actingAs($manager)
        ->put(route('notes.update', $note), [
            'store_id' => $store->id,
            'title' => 'Ops idea',
            'body' => 'Add a queue ticket display.',
            'category' => 'ops',
            'status' => StoreNote::STATUS_DONE,
        ])
        ->assertRedirect(route('notes.show', $note));

    expect($note->fresh()->status)->toBe(StoreNote::STATUS_DONE);
});

test('cannot attach another stores staff or review to a note', function () {
    [$owner, $store] = makeOwnedStore();
    [, $otherStore] = makeOwnedStore();
    $otherStaff = Staff::create([
        'store_id' => $otherStore->id,
        'name' => 'Other',
        'role' => 'Chef',
    ]);
    $otherReview = Review::create([
        'store_id' => $otherStore->id,
        'rating_store' => 3,
        'comment' => 'Other shop',
    ]);

    $this->actingAs($owner)
        ->post(route('notes.store'), [
            'store_id' => $store->id,
            'staff_id' => $otherStaff->id,
            'review_id' => $otherReview->id,
            'title' => 'Bad link',
            'body' => 'Should fail',
            'status' => StoreNote::STATUS_OPEN,
        ])
        ->assertSessionHasErrors(['staff_id', 'review_id']);
});

test('outsider cannot view another stores notes', function () {
    [$owner, $store] = makeOwnedStore();
    $outsider = User::factory()->create();
    $note = StoreNote::create([
        'store_id' => $store->id,
        'user_id' => $owner->id,
        'title' => 'Private',
        'body' => 'Internal',
        'status' => StoreNote::STATUS_OPEN,
    ]);

    $this->actingAs($outsider)
        ->get(route('notes.show', $note))
        ->assertForbidden();
});
