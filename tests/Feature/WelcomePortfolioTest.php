<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class WelcomePortfolioTest extends TestCase
{
    public function test_welcome_portfolio_page_loads_successfully_for_guests(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Review<span>Me</span>', false);
        $response->assertSee('Admin Login');
        $response->assertSee('Login');
        $response->assertDontSee('Login to Your Respective Stores');
        $response->assertDontSee('Switch Store Perspectives');
        $response->assertDontSee('Ready to Elevate Your Store Reputation?');
        $response->assertDontSee('6 Designer QR Stand Styles');
        $response->assertSee(route('language.switch', 'en'), false);
        $response->assertSee(route('language.switch', 'my'), false);
        $response->assertDontSee('Language & Legal');
    }

    public function test_welcome_portfolio_excludes_demo_shops_and_caps_at_six(): void
    {
        $demoUser = User::firstOrCreate(
            ['email' => 'demo@gmail.test'],
            ['name' => 'Demo Admin', 'password' => bcrypt('password')]
        );

        Store::firstOrCreate(
            ['name' => 'Demo Shop 1'],
            ['user_id' => $demoUser->id, 'is_active' => true, 'qr_identifier' => (string) Str::uuid()]
        );

        $realUser = User::factory()->create();
        for ($i = 1; $i <= 8; $i++) {
            Store::firstOrCreate(
                ['name' => "Real Store {$i}"],
                ['user_id' => $realUser->id, 'is_active' => true, 'qr_identifier' => (string) Str::uuid()]
            );
        }

        $response = $this->get('/');
        $response->assertStatus(200);

        /** @var \Illuminate\Support\Collection $dbStores */
        $dbStores = $response->viewData('dbStores');
        $this->assertNotNull($dbStores);
        $this->assertLessThanOrEqual(6, $dbStores->count());

        foreach ($dbStores as $store) {
            $this->assertStringNotContainsStringIgnoringCase('demo', $store->name);
            if ($store->relationLoaded('user') && $store->user) {
                $this->assertNotEquals('demo@gmail.test', $store->user->email);
            }
        }

        /** @var \Illuminate\Support\Collection $demoStores */
        $demoStores = $response->viewData('demoStores');
        $this->assertNotNull($demoStores);
        $this->assertGreaterThanOrEqual(1, $demoStores->count());
        foreach ($demoStores as $dStore) {
            $this->assertStringContainsStringIgnoringCase('demo', $dStore->name);
        }
    }

    public function test_welcome_portfolio_page_displays_admin_portal_links_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Admin Portal');
        $response->assertSee(route('logout'));
    }

    public function test_welcome_page_renders_native_burmese_after_locale_switch(): void
    {
        $this->from('/')->get(route('language.switch', 'my'))->assertRedirect('/');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('ချက်ချင်း ဖောက်သည်သုံးသပ်ချက်', false);
        $response->assertSee('အက်ဒမင် ဝင်ရန်', false);
        $response->assertSee('မြန်မာ', false);
        $response->assertDontSee('Language & Legal');
    }
}
