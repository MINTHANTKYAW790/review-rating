<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Review;
use App\Models\Store;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class WelcomeController extends Controller
{
    public function index(Request $request, QrCodeService $qrCodeService)
    {
        // 1. Fetch real active stores
        $dbStores = collect();
        $totalStoresCount = 0;
        $totalReviewsCount = 0;
        $avgRating = 4.9;

        $demoStores = collect();
        $demoAdminEmail = 'demo@gmail.test';
        $demoPassword = '123123123';

        try {
            if (Schema::hasTable('stores')) {
                $dbStores = Store::query()
                    ->where('is_active', true)
                    ->whereDoesntHave('user', function ($q) use ($demoAdminEmail) {
                        $q->where('email', $demoAdminEmail);
                    })
                    ->where('name', 'not like', '%demo%')
                    ->withAvg('reviews', 'rating_store')
                    ->withCount('reviews')
                    ->latest()
                    ->take(6)
                    ->get();

                $demoStores = Store::query()
                    ->whereHas('user', function ($q) use ($demoAdminEmail) {
                        $q->where('email', $demoAdminEmail);
                    })
                    ->with(['staff', 'manager'])
                    ->withCount(['staff', 'reviews'])
                    ->orderBy('name')
                    ->get();

                $totalStoresCount = Store::query()->where('is_active', true)->count();
            }

            if (Schema::hasTable('reviews')) {
                $totalReviewsCount = Review::query()->count();
                $dbAvg = Review::query()->avg('rating_store');
                if ($dbAvg && $dbAvg > 0) {
                    $avgRating = round($dbAvg, 1);
                }
            }
        } catch (\Throwable $e) {
            // Graceful fallback if migrations/connection pending
        }

        // Curated portfolio stores to ensure a rich, stunning showcase
        $sampleStores = collect([
            (object) [
                'id' => 101,
                'name' => 'The Daily Grind Cafe',
                'slug' => 'the-daily-grind',
                'category' => 'Artisan Coffee & Bakery',
                'address' => '42 Heritage Boulevard, Downtown',
                'phone' => '+1 (555) 234-5678',
                'description' => 'Specialty pour-overs, single-origin roasts, and organic pastries baked fresh every morning.',
                'rating' => 4.9,
                'reviews_count' => 342,
                'is_demo' => false,
                'badge' => 'Top Rated',
                'tagline' => 'Average customer response in under 2 minutes',
                'top_staff' => 'Sarah L. (Head Barista)',
                'image' => asset('images/welcome_hero_qr.jpg'),
            ],
            (object) [
                'id' => 102,
                'name' => 'Azure Bistro & Lounge',
                'slug' => 'azure-bistro',
                'category' => 'Fine Dining & Hospitality',
                'address' => '108 Marina Wharf, Bayfront',
                'phone' => '+1 (555) 987-6543',
                'description' => 'Contemporary coastal cuisine, curated wine cellar, and ambient open-terrace seating.',
                'rating' => 4.8,
                'reviews_count' => 518,
                'is_demo' => false,
                'badge' => 'Hospitality Star',
                'tagline' => '100% review triage rate across 12 table zones',
                'top_staff' => 'Marco V. (Sommelier)',
                'image' => asset('images/welcome_mobile_review.jpg'),
            ],
            (object) [
                'id' => 103,
                'name' => 'Velvet Bloom Boutique',
                'slug' => 'velvet-bloom',
                'category' => 'Fashion & Lifestyle Retail',
                'address' => '77 Silk Street, Fashion District',
                'phone' => '+1 (555) 456-7890',
                'description' => 'Curated designer apparel, handmade accessories, and personalized personal stylist appointments.',
                'rating' => 5.0,
                'reviews_count' => 189,
                'is_demo' => false,
                'badge' => 'Customer Choice',
                'tagline' => '+42% customer retention with QR feedback cards',
                'top_staff' => 'Elena R. (Stylist)',
                'image' => null,
            ],
            (object) [
                'id' => 104,
                'name' => 'Serenity Wellness Spa',
                'slug' => 'serenity-wellness',
                'category' => 'Health & Personal Care',
                'address' => '15 Orchard Grove, Green Hills',
                'phone' => '+1 (555) 321-7654',
                'description' => 'Holistic aromatherapy, mineral salt baths, and rejuvenating massage therapy by certified therapists.',
                'rating' => 4.9,
                'reviews_count' => 274,
                'is_demo' => false,
                'badge' => 'Premier Wellness',
                'tagline' => 'Instant feedback loops to elevate client relaxation',
                'top_staff' => 'Chloe T. (Lead Therapist)',
                'image' => null,
            ],
        ]);

        // Merge real DB stores with sample portfolio stores
        $portfolioStores = $dbStores->map(function ($store) {
            return (object) [
                'id' => $store->id,
                'name' => $store->name,
                'slug' => $store->slug,
                'category' => 'Verified Store Location',
                'address' => $store->address ?: 'Branch Location',
                'phone' => $store->phone ?: 'Inquire via store desk',
                'description' => $store->description ?: 'Trusted local store partner offering high-quality services and verified customer feedback.',
                'rating' => round($store->reviews_avg_rating_store ?: 5.0, 1),
                'reviews_count' => $store->reviews_count ?: 0,
                'is_demo' => false,
                'badge' => 'Verified Partner',
                'tagline' => 'Active QR Code review stand deployed',
                'top_staff' => $store->staff()->first()?->name ?? 'Store Team',
                'image' => $store->logo_url ? asset('storage/' . $store->logo_url) : null,
                'real_store' => true,
            ];
        });

        // Combine DB stores first, then samples to make at least 4 items
        $showcaseStores = $portfolioStores->concat(
            $sampleStores->filter(fn ($sample) => ! $portfolioStores->contains('name', $sample->name))
        )->take(6);

        // 2. Fetch plans
        $plans = collect();
        try {
            if (Schema::hasTable('plans')) {
                $plans = Plan::query()
                    ->where('is_active', true)
                    ->orderBy('price_mmk')
                    ->get();
            }
        } catch (\Throwable $e) {
            // Graceful fallback
        }

        // 3. QR templates
        $qrTemplates = $qrCodeService->templates();

        // 4. Authenticated user & stores
        $user = auth('web')->user();
        $userStores = collect();
        if ($user) {
            $userStores = $user->accessibleStores()->get();
        }

        return view('welcome', [
            'showcaseStores' => $showcaseStores,
            'dbStores' => $dbStores,
            'demoStores' => $demoStores,
            'demoAdminEmail' => $demoAdminEmail,
            'demoPassword' => $demoPassword,
            'plans' => $plans,
            'qrTemplates' => $qrTemplates,
            'user' => $user,
            'userStores' => $userStores,
            'totalStoresCount' => max($totalStoresCount, 120),
            'totalReviewsCount' => max($totalReviewsCount, 15420),
            'avgRating' => $avgRating,
        ]);
    }
}
