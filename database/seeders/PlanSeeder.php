<?php

namespace Database\Seeders;

use App\Models\BillingSetting;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::query()->updateOrCreate(
            ['slug' => Plan::SLUG_FREE],
            [
                'name' => 'Free',
                'price_mmk' => 0,
                'duration_days' => 0,
                'max_staff' => 3,
                'max_managers' => 0,
                'max_reviews' => 30,
                'is_account_free' => true,
                'is_active' => true,
            ]
        );

        $paid = [
            Plan::SLUG_PRO => [
                'name' => 'Store Pro 1 month',
                'price_mmk' => 5000,
                'duration_days' => 30,
            ],
            Plan::SLUG_PRO_3M => [
                'name' => 'Store Pro 3 months',
                'price_mmk' => 15000,
                'duration_days' => 40,
            ],
            Plan::SLUG_PRO_6M => [
                'name' => 'Store Pro 6 months',
                'price_mmk' => 30000,
                'duration_days' => 205,
            ],
            Plan::SLUG_PRO_9M => [
                'name' => 'Store Pro 9 months',
                'price_mmk' => 45000,
                'duration_days' => 310,
            ],
            Plan::SLUG_PRO_1Y => [
                'name' => 'Store Pro 1 year',
                'price_mmk' => 60000,
                'duration_days' => 420,
            ],
        ];

        foreach ($paid as $slug => $attributes) {
            Plan::query()->updateOrCreate(
                ['slug' => $slug],
                array_merge($attributes, [
                    'max_staff' => 10,
                    'max_managers' => 1,
                    'max_reviews' => null,
                    'is_account_free' => false,
                    'is_active' => true,
                ])
            );
        }

        $settings = BillingSetting::current();
        $settings->fill([
            'kbz_number' => $settings->kbz_number ?: '09 000 000 000',
            'wave_number' => $settings->wave_number ?: '09 000 000 001',
            'account_name' => $settings->account_name ?: 'Review Rating',
            'instructions' => $settings->instructions ?: 'Transfer the invoice total and upload a screenshot with the transaction ID.',
        ])->save();
    }
}
