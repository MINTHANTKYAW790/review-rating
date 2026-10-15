<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Review;
use App\Models\Staff;
use App\Models\Store;
use App\Models\StoreSubscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoStoreSeeder extends Seeder
{
    public const DEFAULT_PASSWORD = '123123123';

    public function run(): void
    {
        $password = Hash::make(self::DEFAULT_PASSWORD);

        // 1. Create or update Demo Admin account
        $admin = User::query()->updateOrCreate(
            ['email' => 'demo@gmail.test'],
            [
                'name' => 'Demo Admin',
                'password' => $password,
                'is_active' => true,
            ]
        );
        $admin->syncRoles(['admin']);

        // 2. Define the 3 demo shops with 5, 7, and 10 staff
        $shops = [
            [
                'name' => 'Demo Shop 1',
                'address' => 'No. 101, Main Avenue, Yangon',
                'description' => 'Flagship testing boutique with premium customer experience.',
                'manager_name' => 'Demo Manager 1',
                'manager_email' => 'manager1@demo.test',
                'staff' => [
                    ['name' => 'Ko Aung', 'role' => 'Senior Barista'],
                    ['name' => 'Ma Hla', 'role' => 'Customer Care'],
                    ['name' => 'Mg Kyaw', 'role' => 'Order Specialist'],
                    ['name' => 'Thida Win', 'role' => 'Cashier'],
                    ['name' => 'Zaw Moe', 'role' => 'Table Server'],
                ],
            ],
            [
                'name' => 'Demo Shop 2',
                'address' => 'No. 202, Riverfront Road, Mandalay',
                'description' => 'Culinary & retail testing store featuring rapid QR feedback.',
                'manager_name' => 'Demo Manager 2',
                'manager_email' => 'manager2@demo.test',
                'staff' => [
                    ['name' => 'Tun Tun', 'role' => 'Head Chef'],
                    ['name' => 'Su Su', 'role' => 'Host & Reception'],
                    ['name' => 'Nilar Aung', 'role' => 'Floor Captain'],
                    ['name' => 'Bo Bo', 'role' => 'Bar Specialist'],
                    ['name' => 'Lin Lin', 'role' => 'Order Dispatch'],
                    ['name' => 'Myo Min', 'role' => 'Customer Support'],
                    ['name' => 'San San', 'role' => 'Cashier'],
                ],
            ],
            [
                'name' => 'Demo Shop 3',
                'address' => 'No. 303, Heritage Park, Naypyidaw',
                'description' => 'Multi-zone hospitality store with advanced review triage.',
                'manager_name' => 'Demo Manager 3',
                'manager_email' => 'manager3@demo.test',
                'staff' => [
                    ['name' => 'Nay Lin', 'role' => 'General Supervisor'],
                    ['name' => 'Moe Moe', 'role' => 'Guest Relations'],
                    ['name' => 'Kyaw Kyaw', 'role' => 'Service Lead'],
                    ['name' => 'Aye Aye', 'role' => 'Sommelier'],
                    ['name' => 'Thiha', 'role' => 'Barista Lead'],
                    ['name' => 'Yamin', 'role' => 'Quality Inspector'],
                    ['name' => 'Min Min', 'role' => 'Front Desk'],
                    ['name' => 'Wai Wai', 'role' => 'Concierge'],
                    ['name' => 'Chit Ko', 'role' => 'Logistics & Stock'],
                    ['name' => 'Khin Khin', 'role' => 'Cashier'],
                ],
            ],
        ];

        foreach ($shops as $index => $shopData) {
            // Create demo manager
            $manager = User::query()->updateOrCreate(
                ['email' => $shopData['manager_email']],
                [
                    'name' => $shopData['manager_name'],
                    'password' => $password,
                    'is_active' => true,
                    'created_by' => $admin->id,
                ]
            );
            $manager->syncRoles(['manager']);

            // Create or update store
            $store = Store::query()->updateOrCreate(
                [
                    'user_id' => $admin->id,
                    'name' => $shopData['name'],
                ],
                [
                    'manager_id' => $manager->id,
                    'address' => $shopData['address'],
                    'phone' => '09' . str_pad((string) (900000000 + $index + 1), 9, '0', STR_PAD_LEFT),
                    'email' => Str::slug($shopData['name']) . '@demo.test',
                    'description' => $shopData['description'],
                    'qr_identifier' => (string) Str::uuid(),
                    'is_active' => true,
                ]
            );

            // Create staff
            $createdStaff = [];
            foreach ($shopData['staff'] as $staffData) {
                $createdStaff[] = Staff::query()->updateOrCreate(
                    [
                        'store_id' => $store->id,
                        'name' => $staffData['name'],
                    ],
                    [
                        'role' => $staffData['role'],
                    ]
                );
            }

            // Ensure subscription
            $this->ensureActiveSubscription($store);

            // Seed initial demo reviews
            $this->seedInitialDemoReviews($store, $createdStaff);
        }
    }

    private function ensureActiveSubscription(Store $store): void
    {
        if ($store->subscriptions()->where('status', StoreSubscription::STATUS_ACTIVE)->exists()) {
            return;
        }

        $plan = Plan::query()->where('is_active', true)->where('slug', 'pro')->first()
            ?? Plan::query()->where('is_active', true)->orderByDesc('price_mmk')->first();

        if (! $plan) {
            return;
        }

        StoreSubscription::query()->create([
            'store_id' => $store->id,
            'plan_id' => $plan->id,
            'status' => StoreSubscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => now()->addYear(),
            'price_mmk' => $plan->price_mmk,
        ]);
    }

    private function seedInitialDemoReviews(Store $store, array $staffList): void
    {
        // Only seed if store has no reviews yet
        if ($store->reviews()->exists()) {
            return;
        }

        $reviews = [
            [
                'rating_store' => 5,
                'rating_staff' => 5,
                'comment' => 'Exceptional service and quick attention! Highly recommended for testing.',
                'comment_staff' => 'Very polite and attentive.',
                'follow_up_status' => Review::STATUS_DONE,
                'is_demo' => true,
            ],
            [
                'rating_store' => 5,
                'rating_staff' => 5,
                'comment' => 'Clean atmosphere and seamless QR feedback process.',
                'comment_staff' => 'Handled questions quickly.',
                'follow_up_status' => Review::STATUS_DONE,
                'is_demo' => true,
            ],
            [
                'rating_store' => 4,
                'rating_staff' => 4,
                'comment' => 'Great experience overall. Looking forward to visiting again.',
                'comment_staff' => 'Pleasant service.',
                'follow_up_status' => Review::STATUS_INBOX,
                'is_demo' => true,
            ],
        ];

        foreach ($reviews as $i => $revData) {
            $staff = $staffList[$i % count($staffList)] ?? null;
            Review::query()->create([
                'store_id' => $store->id,
                'staff_id' => $staff?->id,
                'rating_store' => $revData['rating_store'],
                'rating_staff' => $revData['rating_staff'],
                'comment' => $revData['comment'],
                'comment_staff' => $revData['comment_staff'],
                'customer_phone' => '0912345678' . $i,
                'contact_requested' => false,
                'follow_up_status' => $revData['follow_up_status'],
                'ip_address' => '127.0.0.1',
                'device_cookie' => (string) Str::uuid(),
                'is_demo' => true,
            ]);
        }
    }
}
