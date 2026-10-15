<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Staff;
use App\Models\Store;
use App\Models\StoreSubscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoPlatformSeeder extends Seeder
{
    private const PASSWORD = '123123123';

    public function run(): void
    {
        $password = Hash::make(self::PASSWORD);

        $superadmin = User::query()->updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => $password,
                'is_active' => true,
            ]
        );
        $superadmin->syncRoles(['superadmin']);

        $owners = [
            [
                'name' => 'Owner One',
                'email' => 'owner1@example.com',
                'shops' => [
                    [
                        'name' => 'Sunrise Cafe',
                        'address' => '12 Market Street',
                        'staff' => [
                            ['name' => 'Aung Aung', 'role' => 'Barista'],
                            ['name' => 'Hla Hla', 'role' => 'Cashier'],
                        ],
                    ],
                    [
                        'name' => 'Sunrise Bakery',
                        'address' => '14 Market Street',
                        'staff' => [
                            ['name' => 'Ko Ko', 'role' => 'Baker'],
                            ['name' => 'Mya Mya', 'role' => 'Server'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Owner Two',
                'email' => 'owner2@example.com',
                'shops' => [
                    [
                        'name' => 'Moonlight Diner',
                        'address' => '88 River Road',
                        'staff' => [
                            ['name' => 'Tun Tun', 'role' => 'Cook'],
                            ['name' => 'Su Su', 'role' => 'Waiter'],
                            ['name' => 'Su Su 1', 'role' => 'Waiter'],
                            ['name' => 'Su Su 3', 'role' => 'Waiter'],
                            ['name' => 'Su Su 4', 'role' => 'Waiter'],
                            ['name' => 'Su Su 5', 'role' => 'Waiter'],
                            ['name' => 'Su Su 6', 'role' => 'Waiter'],
                            ['name' => 'Su Su 7', 'role' => 'Waiter'],
                            ['name' => 'Su Su 8', 'role' => 'Waiter'],
                            ['name' => 'Su Su 9', 'role' => 'Waiter'],
                        ],
                    ],
                    [
                        'name' => 'Moonlight Tea',
                        'address' => '90 River Road',
                        'staff' => [
                            ['name' => 'Zaw Zaw', 'role' => 'Tea Master'],
                            ['name' => 'Nwe Nwe', 'role' => 'Cashier'],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($owners as $ownerData) {
            $owner = User::query()->updateOrCreate(
                ['email' => $ownerData['email']],
                [
                    'name' => $ownerData['name'],
                    'password' => $password,
                    'is_active' => true,
                ]
            );
            $owner->syncRoles(['admin']);

            foreach ($ownerData['shops'] as $index => $shopData) {
                $store = Store::query()->firstOrCreate(
                    [
                        'user_id' => $owner->id,
                        'name' => $shopData['name'],
                    ],
                    [
                        'address' => $shopData['address'],
                        'phone' => '09'.str_pad((string) (($owner->id * 10) + $index), 9, '0', STR_PAD_LEFT),
                        'email' => Str::slug($shopData['name']).'@example.com',
                        'description' => $shopData['name'].' artisan store.',
                        'qr_identifier' => (string) Str::uuid(),
                        'is_active' => true,
                    ]
                );

                foreach ($shopData['staff'] as $staffData) {
                    Staff::query()->updateOrCreate(
                        [
                            'store_id' => $store->id,
                            'name' => $staffData['name'],
                        ],
                        [
                            'role' => $staffData['role'],
                        ]
                    );
                }

                $this->ensureDemoSubscription($store, $index === 0 && count($shopData['staff']) <= 3);
            }
        }
    }

    private function ensureDemoSubscription(Store $store, bool $useFree): void
    {
        if ($store->subscriptions()->where('status', StoreSubscription::STATUS_ACTIVE)->exists()) {
            return;
        }

        $plan = $useFree ? Plan::free() : Plan::pro();

        if (! $plan) {
            return;
        }

        StoreSubscription::query()->create([
            'store_id' => $store->id,
            'plan_id' => $plan->id,
            'status' => StoreSubscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => $plan->is_account_free ? null : now()->addYear(),
            'price_mmk' => $plan->price_mmk,
        ]);
    }
}
