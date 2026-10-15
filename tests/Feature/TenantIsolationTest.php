<?php

use App\Models\User;

test('owner cannot see another owner manager in the users list or by url', function () {
    seedManagerRoles();

    $ownerA = User::factory()->create();
    $ownerA->assignRole('admin');
    $ownerB = User::factory()->create();
    $ownerB->assignRole('admin');

    $storeB = ownedStore($ownerB, 'Owner B Shop');

    $this->actingAs($ownerB)
        ->post(route('users.store'), [
            'name' => 'Branch Manager B',
            'email' => 'manager.b@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'roles' => ['manager'],
            'store_id' => $storeB->id,
        ])
        ->assertRedirect(route('users.index'));

    $managerB = User::where('email', 'manager.b@example.com')->first();

    $this->actingAs($ownerA)
        ->get(route('users.index'))
        ->assertOk()
        ->assertDontSee('manager.b@example.com')
        ->assertDontSee('Branch Manager B');

    $this->actingAs($ownerA)
        ->get(route('users.show', $managerB))
        ->assertForbidden();

    $this->actingAs($ownerA)
        ->get(route('users.edit', $managerB))
        ->assertForbidden();
});

test('owner cannot open the activity log and superadmin can', function () {
    seedManagerRoles();

    $owner = User::factory()->create();
    $owner->assignRole('admin');

    $this->actingAs($owner)
        ->get(route('activity-log.index'))
        ->assertForbidden();

    $superadmin = User::factory()->create();
    $superadmin->assignRole('superadmin');

    $this->actingAs($superadmin)
        ->get(route('activity-log.index'))
        ->assertOk();
});

test('store manager assignment rejects a manager created by another owner', function () {
    seedManagerRoles();

    $ownerA = User::factory()->create();
    $ownerA->assignRole('admin');
    $ownerB = User::factory()->create();
    $ownerB->assignRole('admin');

    $foreignManager = User::factory()->create(['created_by' => $ownerB->id]);
    $foreignManager->assignRole('manager');

    $storeA = ownedStore($ownerA, 'Owner A Shop');

    $this->actingAs($ownerA)
        ->put(route('store.update', $storeA), [
            'name' => $storeA->name,
            'manager_id' => $foreignManager->id,
        ])
        ->assertSessionHasErrors('manager_id');

    expect($storeA->fresh()->manager_id)->toBeNull();
});
