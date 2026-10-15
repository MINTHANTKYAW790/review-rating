<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('messages.welcome_meta_title', ['app' => config('app.name', 'Review Me')]) }}</title>
    <meta name="description" content="{{ __('messages.welcome_meta_desc') }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Myanmar:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Welcome Portfolio Custom Styles -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>

<body>

    <!-- =====================================================================
         NAVIGATION BAR
         ===================================================================== -->
    <nav class="site-nav" id="siteNav">
        <div class="container">
            <div class="nav-wrap">
                <!-- Brand Logo -->
                <a href="{{ route('welcome') }}" class="nav-brand">
                    <div class="brand-icon-box">
                        <img src="{{ asset('review-me.png') }}" alt="Review Me" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa-solid fa-star\'></i>';">
                    </div>
                    <div class="brand-text-group">
                        <span class="brand-title">Review<span>Me</span></span>
                        <span class="brand-tag">{{ __('messages.welcome_brand_tag') }}</span>
                    </div>
                </a>

                <!-- Navigation Links (+ drawer CTAs on small screens) -->
                <ul class="nav-links" id="navLinks">
                    <li><a href="#features" class="nav-link">{{ __('messages.welcome_nav_features') }}</a></li>
                    <li><a href="#demo-sandbox" class="nav-link"><i class="fa-solid fa-flask" style="opacity: 0.8; margin-right: 4px;"></i> {{ __('messages.demo_sandbox') }}</a></li>
                    <li><a href="#portfolio" class="nav-link">{{ __('messages.welcome_nav_portfolio') }}</a></li>
                    {{-- <li><a href="#qr-showcase" class="nav-link">{{ __('messages.welcome_nav_qr') }}</a></li> --}}
                    {{-- <li><a href="#admin-portal" class="nav-link">{{ __('messages.welcome_nav_admin') }}</a></li> --}}
                    <li><a href="#pricing" class="nav-link">{{ __('messages.welcome_nav_plans') }}</a></li>
                    <li><a href="#faq" class="nav-link">{{ __('messages.welcome_nav_faq') }}</a></li>
                    <li class="nav-drawer-ctas">
                        @include('welcome.partials.nav-auth-actions', ['placement' => 'drawer'])
                    </li>
                </ul>

                <div class="nav-actions">
                    @include('welcome.partials.lang-switcher')
                    <div class="nav-cta-bar">
                        @include('welcome.partials.nav-auth-actions', ['placement' => 'bar'])
                    </div>
                    <button class="nav-toggle" id="navToggle" type="button" aria-label="{{ __('messages.welcome_nav_toggle') }}" aria-expanded="false" aria-controls="navLinks">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- =====================================================================
         HERO SECTION
         ===================================================================== -->
    <header class="hero-section">
        <div class="hero-mesh"></div>
        <div class="container">
            <div class="hero-grid">
                <!-- Left: Hero Headline & CTAs -->
                <div class="hero-content">
                    <div class="hero-pill">
                        <span class="hero-pill-dot"></span>
                        <span>{{ __('messages.welcome_hero_pill') }}</span>
                    </div>

                    <h1 class="hero-title">
                        {!! __('messages.welcome_hero_title', ['highlight' => '<span class="gradient-text">'.e(__('messages.welcome_hero_title_highlight')).'</span>']) !!}
                    </h1>

                    <p class="hero-subtitle">
                        {{ __('messages.welcome_hero_subtitle') }}
                    </p>

                    <div class="hero-ctas">
                        @auth
                        <a href="{{ auth()->user()->isSuperAdmin() ? route('superadmin.dashboard') : route('dashboard') }}" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-gauge"></i>
                            <span>{{ __('messages.welcome_go_dashboard') }}</span>
                        </a>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg" id="heroAdminLoginBtn">
                            <i class="fa-solid fa-key"></i>
                            <span>Login</span>
                        </a>
                        <a href="#demo-sandbox" class="btn btn-outline-primary btn-lg">
                            <i class="fa-solid fa-flask"></i>
                            <span>Try Demo Stores</span>
                        </a>
                        @endauth
                        <a href="#portfolio" class="btn btn-ghost btn-lg">
                            <span>{{ __('messages.welcome_explore_portfolio') }}</span>
                            <i class="fa-solid fa-arrow-down"></i>
                        </a>
                    </div>

                    <div class="hero-trust-row">
                        <div class="hero-trust-item">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>{{ __('messages.welcome_trust_qr') }}</span>
                        </div>
                        <div class="hero-trust-item">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>{{ __('messages.welcome_trust_no_app') }}</span>
                        </div>
                        <div class="hero-trust-item">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>{{ __('messages.welcome_trust_triage') }}</span>
                        </div>
                        <div class="hero-trust-item">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>{{ __('messages.welcome_trust_auth') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Hero Visual Frame with Floating Badges -->
                <div class="hero-visual">
                    <div class="hero-preview-frame">
                        <img src="{{ asset('images/welcome_hero_qr.jpg') }}" alt="{{ __('messages.welcome_hero_img_alt') }}" class="hero-main-img">
                    </div>

                    <!-- Floating Badge 1: New Review -->
                    <div class="float-card float-card-top">
                        <div class="float-icon float-icon-gold">
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <div>
                            <div class="float-title">{{ __('messages.welcome_float_review_title') }}</div>
                            <div class="float-sub">{{ __('messages.welcome_float_review_sub') }}</div>
                        </div>
                    </div>

                    <!-- Floating Badge 2: Triage Resolution -->
                    <div class="float-card float-card-bottom">
                        <div class="float-icon float-icon-green">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <div>
                            <div class="float-title">{{ __('messages.welcome_float_triage_title') }}</div>
                            <div class="float-sub">{{ __('messages.welcome_float_triage_sub') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- =====================================================================
         METRICS RIBBON
         ===================================================================== -->
    <section class="metrics-ribbon">
        <div class="container">
            <div class="metrics-grid">
                <div class="metric-item">
                    <div class="metric-number">{{ number_format($totalReviewsCount) }}<span>+</span></div>
                    <div class="metric-label">{{ __('messages.welcome_metric_reviews') }}</div>
                </div>
                <div class="metric-item">
                    <div class="metric-number">{{ number_format($totalStoresCount) }}<span>+</span></div>
                    <div class="metric-label">{{ __('messages.welcome_metric_stores') }}</div>
                </div>
                <div class="metric-item">
                    <div class="metric-number">{{ $avgRating }}<span>★</span></div>
                    <div class="metric-label">{{ __('messages.welcome_metric_rating') }}</div>
                </div>
                <div class="metric-item">
                    <div class="metric-number">99.4<span>%</span></div>
                    <div class="metric-label">{{ __('messages.welcome_metric_triage') }}</div>
                </div>
            </div>
        </div>
    </section>

    {{-- =====================================================================
         ADMIN PERSPECTIVE & STORE PORTAL SECTION
         (Dedicated Store Login & Branch Perspective Switcher)
         =====================================================================
    <section class="section" id="admin-portal">
        <div class="container">
            <div class="section-head">
                <span class="section-badge">{{ __('messages.welcome_admin_badge') }}</span>
    <h2 class="section-title">{{ __('messages.welcome_admin_title') }}</h2>
    <p class="section-desc">
        {{ __('messages.welcome_admin_desc') }}
    </p>
    </div>

    <div class="admin-portal-card">
        <div class="admin-portal-grid">
            <!-- Left: Interactive Perspective Simulator -->
            <div>
                <div class="portal-badge">
                    <i class="fa-solid fa-sliders"></i>
                    <span>{{ __('messages.welcome_portal_badge') }}</span>
                </div>
                <h3 class="portal-title">{{ __('messages.welcome_portal_title') }}</h3>
                <p class="portal-desc">
                    {{ __('messages.welcome_portal_desc') }}
                </p>

                <div class="store-perspective-tabs" id="perspectiveTabs">
                    <button type="button" class="perspective-tab active" data-store-name="Sunrise Cafe" data-store-role="{{ __('messages.welcome_role_owner') }}" data-store-plan="Store Pro" data-staff-count="6" data-reviews-count="384" data-rating="4.9 ★" data-manager="Owner One" data-slug="sunrise-cafe">
                        <i class="fa-solid fa-mug-hot"></i>
                        <span>Sunrise Cafe</span>
                    </button>
                    <button type="button" class="perspective-tab" data-store-name="Sunrise Bakery" data-store-role="{{ __('messages.welcome_role_owner') }}" data-store-plan="Store Pro" data-staff-count="4" data-reviews-count="219" data-rating="4.8 ★" data-manager="Owner One" data-slug="sunrise-bakery">
                        <i class="fa-solid fa-bread-slice"></i>
                        <span>Sunrise Bakery</span>
                    </button>
                    <button type="button" class="perspective-tab" data-store-name="Moonlight Diner" data-store-role="{{ __('messages.welcome_role_manager') }}" data-store-plan="Store Pro Plus" data-staff-count="10" data-reviews-count="592" data-rating="4.9 ★" data-manager="Tun Tun" data-slug="moonlight-diner">
                        <i class="fa-solid fa-utensils"></i>
                        <span>Moonlight Diner</span>
                    </button>
                    <button type="button" class="perspective-tab" data-store-name="Moonlight Tea" data-store-role="{{ __('messages.welcome_role_owner') }}" data-store-plan="Starter Free" data-staff-count="3" data-reviews-count="145" data-rating="4.7 ★" data-manager="Owner Two" data-slug="moonlight-tea">
                        <i class="fa-solid fa-leaf"></i>
                        <span>Moonlight Tea</span>
                    </button>
                </div>

                <!-- Active Perspective Panel -->
                <div class="perspective-preview-panel">
                    <div class="perspective-panel-header">
                        <div class="perspective-store-info">
                            <div class="perspective-store-avatar" id="perspectiveAvatar">
                                <i class="fa-solid fa-store"></i>
                            </div>
                            <div>
                                <div class="perspective-store-name" id="perspectiveStoreName">Sunrise Cafe</div>
                                <div class="perspective-store-role" id="perspectiveStoreRole">{{ __('messages.welcome_managed_by', ['name' => 'Owner One', 'role' => __('messages.welcome_role_owner')]) }}</div>
                            </div>
                        </div>
                        <div class="perspective-status-badge">
                            <i class="fa-solid fa-circle"></i>
                            <span id="perspectiveStorePlan">{{ __('messages.welcome_plan_active', ['plan' => 'Store Pro']) }}</span>
                        </div>
                    </div>

                    <div class="perspective-stats-row">
                        <div class="perspective-stat-box">
                            <div class="perspective-stat-val" id="perspectiveStatReviews">384</div>
                            <div class="perspective-stat-lbl">{{ __('messages.welcome_stat_reviews') }}</div>
                        </div>
                        <div class="perspective-stat-box">
                            <div class="perspective-stat-val" id="perspectiveStatRating">4.9 ★</div>
                            <div class="perspective-stat-lbl">{{ __('messages.welcome_stat_score') }}</div>
                        </div>
                        <div class="perspective-stat-box">
                            <div class="perspective-stat-val" id="perspectiveStatStaff">6</div>
                            <div class="perspective-stat-lbl">{{ __('messages.welcome_stat_staff') }}</div>
                        </div>
                    </div>

                    <div class="perspective-actions">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm flex-fill" id="perspectiveLoginBtn">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            <span>{{ __('messages.welcome_login_manage') }}</span>
                        </a>
                        <a href="{{ url('/public/qr/sunrise-cafe') }}" target="_blank" class="btn btn-outline-primary btn-sm" id="perspectivePreviewQrBtn" style="color:#fff !important; border-color: rgba(255,255,255,0.3);">
                            <i class="fa-solid fa-qrcode"></i>
                            <span>{{ __('messages.welcome_test_qr') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right: Quick Admin Login Card -->
            <div class="admin-login-box">
                <div class="login-box-header">
                    <h4 class="login-box-title">{{ __('messages.welcome_login_box_title') }}</h4>
                    <p class="login-box-sub">{{ __('messages.welcome_login_box_sub') }}</p>
                </div>

                <div class="login-buttons-stack">
                    <!-- Direct Email Login -->
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100">
                        <i class="fa-solid fa-envelope"></i>
                        <span>{{ __('messages.welcome_sign_in_email') }}</span>
                    </a>

                    <!-- Google Login Button -->
                    @if (Route::has('auth.google'))
                    <a href="{{ route('auth.google') }}" class="login-google-btn">
                        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google">
                        <span>{{ __('messages.welcome_continue_google') }}</span>
                    </a>
                    @endif

                    @if (Route::has('register'))
                    <div class="login-divider">{{ __('messages.welcome_or_create') }}</div>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>{{ __('messages.welcome_register_store') }}</span>
                    </a>
                    @endif
                </div>

                <!-- Demo Accounts Helper for Quick Evaluation -->
                <div class="demo-credentials-card">
                    <div class="demo-cred-title">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>{{ __('messages.welcome_demo_title') }}</span>
                    </div>
                    <div class="demo-cred-row">
                        <span>{{ __('messages.welcome_demo_admin') }}</span>
                        <code>owner1@example.com</code>
                    </div>
                    <div class="demo-cred-row">
                        <span>{{ __('messages.welcome_demo_password') }}</span>
                        <code>123123123</code>
                    </div>
                    <div class="demo-cred-row" style="margin-top:0.4rem;">
                        <span>{{ __('messages.welcome_demo_super') }}</span>
                        <code>superadmin@example.com</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </section>
    --}}

    <!-- =====================================================================
         STORE PORTFOLIO SHOWCASE
         ===================================================================== -->
    <section class="section section-alt" id="portfolio">
        <div class="container">
            <div class="section-head">
                <span class="section-badge">{{ __('messages.welcome_portfolio_badge') }}</span>
                <h2 class="section-title">{{ __('messages.welcome_portfolio_title') }}</h2>
                <p class="section-desc">
                    {{ __('messages.welcome_portfolio_desc') }}
                </p>
            </div>

            <div class="portfolio-grid">
                @forelse ($dbStores as $store)
                <div class="store-card">
                    <div class="store-card-header">
                        @if (!empty($store->logo_url))
                        <img src="{{ asset('storage/' . $store->logo_url) }}" alt="{{ $store->name }}">
                        @else
                        <div style="display:flex; align-items:center; justify-content:center; height:100%; color:#fff; font-size:3rem; background: linear-gradient(135deg, var(--navy-850) 0%, var(--primary) 100%);">
                            <i class="fa-solid fa-shop"></i>
                        </div>
                        @endif
                        <div class="store-card-badge">
                            <i class="fa-solid fa-certificate"></i>
                            <span>{{ __('messages.welcome_verified_partner') }}</span>
                        </div>
                    </div>

                    <div class="store-card-body">
                        <div class="store-category">{{ $store->address ?? 'Verified Store Location' }}</div>
                        <h3 class="store-name">{{ $store->name }}</h3>
                        @php
                        $cleanDesc = trim(str_ireplace(['demo branch.', 'demo branch'], '', $store->description ?? ''));
                        @endphp
                        <p class="store-desc">{{ $cleanDesc ?: __('messages.welcome_store_default_desc') }}</p>
                    </div>
                </div>
                @empty
                <div class="portfolio-empty">
                    <i class="fa-solid fa-store-slash"></i>
                    <p>{{ __('messages.welcome_no_stores_portfolio') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- =====================================================================
         INTERACTIVE DEMO TESTING & ADMIN SANDBOX
         ===================================================================== -->
    <section class="section demo-testing-section" id="demo-sandbox">
        <div class="container">
            <div class="section-head text-center">
                <span class="section-badge"><i class="fa-solid fa-flask"></i> Interactive Testing Sandbox</span>
                <h2 class="section-title">Test Drive Review Me as Admin & Customer</h2>
                <p class="section-desc">
                    Experience Review Me in action. Test the admin portal to manage stores, staff, and view feedback, or submit live customer reviews and star ratings on any demo store.
                </p>
            </div>

            <!-- Demo Admin Credentials Box -->
            <div class="demo-admin-box">
                <div class="demo-admin-left">
                    <div class="demo-admin-badge">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Testing Admin Account</span>
                    </div>
                    <div class="demo-credentials-grid">
                        <div class="demo-cred-pill">
                            <span class="cred-label"><i class="fa-solid fa-envelope"></i> Email:</span>
                            <strong class="cred-value">{{ $demoAdminEmail }}</strong>
                        </div>
                        <div class="demo-cred-pill">
                            <span class="cred-label"><i class="fa-solid fa-key"></i> Password:</span>
                            <strong class="cred-value">{{ $demoPassword }}</strong>
                        </div>
                        <div class="demo-cred-pill">
                            <span class="cred-label"><i class="fa-solid fa-user-shield"></i> Role:</span>
                            <strong class="cred-value">Admin (Owner of all 3 Demo Shops)</strong>
                        </div>
                    </div>
                </div>
                <div class="demo-admin-right">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Login as Demo Admin</span>
                    </a>
                </div>
            </div>

            <!-- Midnight Auto-Purge Notice Ribbon -->
            <div class="demo-purge-notice">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span><strong>Daily Midnight Reset:</strong> All ratings, reviews, and customer feedback on demo stores are automatically wiped every midnight at 00:00. Feel free to submit test ratings!</span>
            </div>

            <!-- 3 Demo Stores Grid -->
            <div class="demo-stores-grid">
                @forelse ($demoStores as $dStore)
                <div class="demo-store-card">
                    <div class="demo-card-head">
                        <div class="demo-card-top-row">
                            <span class="demo-store-tag">
                                <i class="fa-solid fa-store"></i> Demo Store
                            </span>
                            <span class="demo-staff-count-badge">
                                <i class="fa-solid fa-users"></i> {{ $dStore->staff_count ?? $dStore->staff->count() }} Staff
                            </span>
                        </div>
                        <h3 class="demo-card-title">{{ $dStore->name }}</h3>
                        <div class="demo-card-address">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>{{ $dStore->address }}</span>
                        </div>
                    </div>

                    <div class="demo-card-body">
                        <!-- Manager Information -->
                        <div class="demo-manager-info">
                            <div class="demo-manager-avatar">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <div>
                                <div style="font-weight: 700; color: var(--text-dark);">
                                    {{ $dStore->manager?->name ?? 'Demo Manager' }}
                                </div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); font-family: monospace;">
                                    {{ $dStore->manager?->email ?? 'manager@demo.test' }} &bull; Pass: {{ $demoPassword }}
                                </div>
                            </div>
                        </div>

                        <!-- Staff Preview List -->
                        <div class="demo-staff-preview">
                            <div class="demo-staff-preview-title">
                                <span>Assigned Staff ({{ $dStore->staff->count() }})</span>
                            </div>
                            <div class="demo-staff-chips">
                                @foreach ($dStore->staff as $st)
                                <span class="demo-staff-chip">
                                    <i class="fa-solid fa-user"></i> {{ $st->name }} ({{ $st->role }})
                                </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Testing Action CTAs -->
                        <div class="demo-card-actions">
                            <a href="{{ route('public.review', $dStore->slug) }}" target="_blank" class="btn btn-primary">
                                <i class="fa-solid fa-star"></i>
                                <span>Give Review & Rating</span>
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                <i class="fa-solid fa-gauge"></i>
                                <span>Admin Login to Manage</span>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="portfolio-empty" style="grid-column: 1 / -1;">
                    <i class="fa-solid fa-store-slash"></i>
                    <p>Demo stores are currently initializing. Run <code>php artisan db:seed --class=DemoStoreSeeder</code> to seed them.</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- =====================================================================
         STYLED QR STANDS SHOWCASE
         =====================================================================
    <section class="section" id="qr-showcase">
        <div class="container">
            <div class="section-head">
                <span class="section-badge">{{ __('messages.welcome_qr_badge') }}</span>
    <h2 class="section-title">{{ __('messages.welcome_qr_title') }}</h2>
    <p class="section-desc">
        {{ __('messages.welcome_qr_desc') }}
    </p>
    </div>

    <div class="qr-showcase-wrap">
        <div class="qr-styles-grid">
            <!-- Left: QR Template Switcher -->
            <div class="qr-selector-list" id="qrTemplateList">
                @php
                $templateIcons = [
                'classic' => 'fa-certificate',
                'neon' => 'fa-wand-magic-sparkles',
                'royal' => 'fa-crown',
                'dotted' => 'fa-circle-dot',
                'sunburst' => 'fa-sun',
                'tech' => 'fa-microchip',
                ];
                @endphp
                @foreach ($qrTemplates as $key => $tmpl)
                <div class="qr-style-option {{ $loop->first ? 'active' : '' }}" data-style="{{ $key }}" data-name="{{ $tmpl['name'] }}" data-desc="{{ $tmpl['description'] }}">
                    <div class="qr-opt-info">
                        <div class="qr-opt-icon">
                            <i class="fa-solid {{ $templateIcons[$key] ?? 'fa-qrcode' }}"></i>
                        </div>
                        <div>
                            <div class="qr-opt-name">{{ $tmpl['name'] }}</div>
                            <div class="qr-opt-desc">{{ $tmpl['description'] }}</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-muted"></i>
                </div>
                @endforeach
            </div>

            <!-- Right: Acrylic Table Stand Live Mockup -->
            <div class="qr-mockup-stage">
                <div class="acrylic-stand" id="acrylicStandMockup">
                    <div class="stand-store-name" id="standStoreName">Sunrise Cafe</div>
                    <div class="stand-cta-text" id="standStyleLabel">Classic Clean Design</div>

                    <div class="stand-qr-box" id="standQrBox">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100%" height="100%">
                            <!-- Stylized QR Code Graphic -->
                            <rect width="100" height="100" fill="#ffffff" />
                            <rect x="10" y="10" width="26" height="26" fill="#141655" rx="4" />
                            <rect x="15" y="15" width="16" height="16" fill="#ffffff" rx="2" />
                            <rect x="19" y="19" width="8" height="8" fill="#6868AC" rx="1" />

                            <rect x="64" y="10" width="26" height="26" fill="#141655" rx="4" />
                            <rect x="69" y="15" width="16" height="16" fill="#ffffff" rx="2" />
                            <rect x="73" y="19" width="8" height="8" fill="#6868AC" rx="1" />

                            <rect x="10" y="64" width="26" height="26" fill="#141655" rx="4" />
                            <rect x="15" y="69" width="16" height="16" fill="#ffffff" rx="2" />
                            <rect x="19" y="73" width="8" height="8" fill="#6868AC" rx="1" />

                            <!-- QR Pattern Pixels -->
                            <rect x="42" y="12" width="6" height="6" fill="#141655" />
                            <rect x="52" y="12" width="6" height="6" fill="#6868AC" />
                            <rect x="42" y="24" width="6" height="6" fill="#141655" />
                            <rect x="42" y="36" width="6" height="6" fill="#6868AC" />
                            <rect x="52" y="36" width="6" height="6" fill="#141655" />
                            <rect x="12" y="44" width="6" height="6" fill="#141655" />
                            <rect x="24" y="44" width="6" height="6" fill="#6868AC" />
                            <rect x="64" y="44" width="6" height="6" fill="#141655" />
                            <rect x="76" y="44" width="6" height="6" fill="#6868AC" />
                            <rect x="44" y="52" width="6" height="6" fill="#141655" />
                            <rect x="56" y="52" width="6" height="6" fill="#6868AC" />
                            <rect x="44" y="64" width="6" height="6" fill="#6868AC" />
                            <rect x="56" y="64" width="6" height="6" fill="#141655" />
                            <rect x="72" y="64" width="6" height="6" fill="#141655" />
                            <rect x="84" y="64" width="6" height="6" fill="#6868AC" />
                            <rect x="44" y="76" width="6" height="6" fill="#141655" />
                            <rect x="64" y="76" width="6" height="6" fill="#6868AC" />
                            <rect x="80" y="80" width="8" height="8" fill="#141655" />
                        </svg>
                    </div>

                    <div class="stand-stars">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <div class="stand-footnote">{{ __('messages.welcome_qr_footnote') }}</div>
                </div>
                <div class="acrylic-stand-base"></div>

                <div class="mt-4 text-center">
                    <a href="{{ route('login') }}" class="btn btn-navy btn-sm">
                        <i class="fa-solid fa-download"></i>
                        <span>{{ __('messages.welcome_qr_download') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    </div>
    </section>
    --}}

    <!-- =====================================================================
         CORE PLATFORM FEATURES
         ===================================================================== -->
    <section class="section section-alt" id="features">
        <div class="container">
            <div class="section-head">
                <span class="section-badge">{{ __('messages.welcome_features_badge') }}</span>
                <h2 class="section-title">{{ __('messages.welcome_features_title') }}</h2>
                <p class="section-desc">
                    {{ __('messages.welcome_features_desc') }}
                </p>
            </div>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon-wrap">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <h3 class="feature-title">{{ __('messages.welcome_feat_1_title') }}</h3>
                    <p class="feature-text">
                        {{ __('messages.welcome_feat_1_text') }}
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrap">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <h3 class="feature-title">{{ __('messages.welcome_feat_2_title') }}</h3>
                    <p class="feature-text">
                        {{ __('messages.welcome_feat_2_text') }}
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrap">
                        <i class="fa-solid fa-inbox"></i>
                    </div>
                    <h3 class="feature-title">{{ __('messages.welcome_feat_3_title') }}</h3>
                    <p class="feature-text">
                        {{ __('messages.welcome_feat_3_text') }}
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrap">
                        <i class="fa-solid fa-diagram-project"></i>
                    </div>
                    <h3 class="feature-title">{{ __('messages.welcome_feat_4_title') }}</h3>
                    <p class="feature-text">
                        {{ __('messages.welcome_feat_4_text') }}
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrap">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <h3 class="feature-title">{{ __('messages.welcome_feat_5_title') }}</h3>
                    <p class="feature-text">
                        {{ __('messages.welcome_feat_5_text') }}
                    </p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon-wrap">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="feature-title">{{ __('messages.welcome_feat_6_title') }}</h3>
                    <p class="feature-text">
                        {{ __('messages.welcome_feat_6_text') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- =====================================================================
         PRICING & PLANS SECTION
         ===================================================================== -->
    <section class="section" id="pricing">
        <div class="container">
            <div class="section-head">
                <span class="section-badge">{{ __('messages.welcome_pricing_badge') }}</span>
                <h2 class="section-title">{{ __('messages.welcome_pricing_title') }}</h2>
                <p class="section-desc">
                    {{ __('messages.welcome_pricing_desc') }}
                </p>
            </div>

            <div class="pricing-grid">
                <!-- Free Plan -->
                <div class="pricing-card">
                    <h3 class="pricing-plan-name">{{ __('messages.welcome_plan_starter') }}</h3>
                    <p class="pricing-plan-desc">{{ __('messages.welcome_plan_starter_desc') }}</p>

                    <div class="pricing-price-wrap">
                        <span class="pricing-amount">0</span>
                        <span class="pricing-period">{{ __('messages.welcome_price_forever') }}</span>
                    </div>

                    <ul class="pricing-features-list">
                        <li class="pricing-feature-item">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ __('messages.welcome_feat_1_store') }}</span>
                        </li>
                        <li class="pricing-feature-item">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ __('messages.welcome_feat_3_staff') }}</span>
                        </li>
                        <li class="pricing-feature-item">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ __('messages.welcome_feat_30_reviews') }}</span>
                        </li>
                        <li class="pricing-feature-item">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ __('messages.welcome_feat_classic_qr') }}</span>
                        </li>
                        <li class="pricing-feature-item muted">
                            <i class="fa-solid fa-xmark"></i>
                            <span>{{ __('messages.welcome_feat_branch_roles') }}</span>
                        </li>
                        <li class="pricing-feature-item muted">
                            <i class="fa-solid fa-xmark"></i>
                            <span>{{ __('messages.welcome_feat_unlimited') }}</span>
                        </li>
                    </ul>

                    @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary w-100">{{ __('messages.welcome_current_plan') }}</a>
                    @else
                    <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">{{ __('messages.welcome_start_free') }}</a>
                    @endauth
                </div>

                <!-- Pro Monthly (Featured) -->
                <div class="pricing-card featured">
                    <div class="pricing-featured-badge">{{ __('messages.welcome_most_popular') }}</div>
                    <h3 class="pricing-plan-name">{{ __('messages.welcome_plan_pro_month') }}</h3>
                    <p class="pricing-plan-desc">{{ __('messages.welcome_plan_pro_month_desc') }}</p>

                    <div class="pricing-price-wrap">
                        <span class="pricing-amount">5,000</span>
                        <span class="pricing-period">{{ __('messages.welcome_price_30_days') }}</span>
                    </div>

                    <ul class="pricing-features-list">
                        <li class="pricing-feature-item">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ __('messages.welcome_feat_unlimited_reviews') }}</span>
                        </li>
                        <li class="pricing-feature-item">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ __('messages.welcome_feat_10_staff') }}</span>
                        </li>
                        <li class="pricing-feature-item">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ __('messages.welcome_feat_1_manager') }}</span>
                        </li>
                        <li class="pricing-feature-item">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ __('messages.welcome_feat_6_qr') }}</span>
                        </li>
                        <li class="pricing-feature-item">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ __('messages.welcome_feat_prints') }}</span>
                        </li>
                        <li class="pricing-feature-item">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ __('messages.welcome_feat_triage_analytics') }}</span>
                        </li>
                    </ul>

                    @auth
                    <a href="{{ route('billing.index') }}" class="btn btn-primary w-100">{{ __('messages.welcome_upgrade_pro') }}</a>
                    @else
                    <a href="{{ route('login') }}" class="btn btn-primary w-100">
                        <i class="fa-solid fa-key"></i>
                        <span>{{ __('messages.welcome_login_subscribe') }}</span>
                    </a>
                    @endauth
                </div>

                <!-- Pro 1 Year -->
                <div class="pricing-card">
                    <h3 class="pricing-plan-name">{{ __('messages.welcome_plan_pro_year') }}</h3>
                    <p class="pricing-plan-desc">{{ __('messages.welcome_plan_pro_year_desc') }}</p>

                    <div class="pricing-price-wrap">
                        <span class="pricing-amount">60,000</span>
                        <span class="pricing-period">{{ __('messages.welcome_price_420_days') }}</span>
                    </div>

                    <ul class="pricing-features-list">
                        <li class="pricing-feature-item">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ __('messages.welcome_feat_420_days') }}</span>
                        </li>
                        <li class="pricing-feature-item">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ __('messages.welcome_feat_unlimited_reviews') }}</span>
                        </li>
                        <li class="pricing-feature-item">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ __('messages.welcome_feat_10_staff_store') }}</span>
                        </li>
                        <li class="pricing-feature-item">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ __('messages.welcome_feat_managers') }}</span>
                        </li>
                        <li class="pricing-feature-item">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ __('messages.welcome_feat_priority_pay') }}</span>
                        </li>
                        <li class="pricing-feature-item">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ __('messages.welcome_feat_vip') }}</span>
                        </li>
                    </ul>

                    @auth
                    <a href="{{ route('billing.index') }}" class="btn btn-outline-primary w-100">{{ __('messages.welcome_select_annual') }}</a>
                    @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">
                        <i class="fa-solid fa-key"></i>
                        <span>{{ __('messages.welcome_login_subscribe') }}</span>
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <!-- =====================================================================
         TESTIMONIALS & SOCIAL PROOF
         ===================================================================== -->
    <section class="section section-alt">
        <div class="container">
            <div class="section-head">
                <span class="section-badge">{{ __('messages.welcome_stories_badge') }}</span>
                <h2 class="section-title">{{ __('messages.welcome_stories_title') }}</h2>
                <p class="section-desc">
                    {{ __('messages.welcome_stories_desc') }}
                </p>
            </div>

            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div>
                        <div class="testimonial-stars">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <p class="testimonial-quote">
                            {{ __('messages.welcome_quote_1') }}
                        </p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">SC</div>
                        <div>
                            <div class="author-name">Owner One</div>
                            <div class="author-store">{{ __('messages.welcome_quote_1_role') }}</div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div>
                        <div class="testimonial-stars">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <p class="testimonial-quote">
                            {{ __('messages.welcome_quote_2') }}
                        </p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">MD</div>
                        <div>
                            <div class="author-name">Owner Two</div>
                            <div class="author-store">{{ __('messages.welcome_quote_2_role') }}</div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div>
                        <div class="testimonial-stars">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <p class="testimonial-quote">
                            {{ __('messages.welcome_quote_3') }}
                        </p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">AB</div>
                        <div>
                            <div class="author-name">Elena R.</div>
                            <div class="author-store">{{ __('messages.welcome_quote_3_role') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =====================================================================
         FAQ SECTION
         ===================================================================== -->
    <section class="section" id="faq">
        <div class="container">
            <div class="section-head">
                <span class="section-badge">{{ __('messages.welcome_faq_badge') }}</span>
                <h2 class="section-title">{{ __('messages.welcome_faq_title') }}</h2>
                <p class="section-desc">
                    {{ __('messages.welcome_faq_desc') }}
                </p>
            </div>

            <div class="faq-list">
                <div class="faq-item open">
                    <div class="faq-question">
                        <span>{{ __('messages.welcome_faq_1_q') }}</span>
                        <i class="fa-solid fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        {!! __('messages.welcome_faq_1_a', ['login' => '<strong>'.e(__('messages.welcome_admin_login')).'</strong>', 'path' => '<code>/login</code>']) !!}
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>{{ __('messages.welcome_faq_2_q') }}</span>
                        <i class="fa-solid fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        {{ __('messages.welcome_faq_2_a') }}
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>{{ __('messages.welcome_faq_3_q') }}</span>
                        <i class="fa-solid fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        {{ __('messages.welcome_faq_3_a') }}
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>{{ __('messages.welcome_faq_4_q') }}</span>
                        <i class="fa-solid fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        {!! __('messages.welcome_faq_4_a', ['qr' => '<strong>'.e(__('messages.welcome_faq_qr_label')).'</strong>']) !!}
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span>{{ __('messages.welcome_faq_5_q') }}</span>
                        <i class="fa-solid fa-chevron-down faq-icon"></i>
                    </div>
                    <div class="faq-answer">
                        {{ __('messages.welcome_faq_5_a') }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- =====================================================================
         CTA BANNER
         =====================================================================
    <section class="section section-alt">
        <div class="container">
            <div class="cta-banner">
                <h2 class="cta-banner-title">{{ __('messages.welcome_cta_title') }}</h2>
    <p class="cta-banner-desc">
        {{ __('messages.welcome_cta_desc') }}
    </p>
    <div class="cta-banner-buttons">
        @auth
        <a href="{{ auth()->user()->isSuperAdmin() ? route('superadmin.dashboard') : route('dashboard') }}" class="btn btn-primary btn-lg">
            <i class="fa-solid fa-gauge-high"></i>
            <span>{{ __('messages.welcome_go_admin_dashboard') }}</span>
        </a>
        @else
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
            <i class="fa-solid fa-key"></i>
            <span>{{ __('messages.welcome_admin_login_store') }}</span>
        </a>
        @if (Route::has('register'))
        <a href="{{ route('register') }}" class="btn btn-gold btn-lg">
            <i class="fa-solid fa-rocket"></i>
            <span>{{ __('messages.welcome_register_free') }}</span>
        </a>
        @endif
        @endauth
    </div>
    </div>
    </div>
    </section>
    --}}

    <!-- =====================================================================
         FOOTER
         ===================================================================== -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Col 1: Brand Info -->
                <div>
                    <a href="{{ route('welcome') }}" class="nav-brand" style="color:#fff;">
                        <div class="brand-icon-box">
                            <img src="{{ asset('review-me.png') }}" alt="Review Me" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa-solid fa-star\'></i>';">
                        </div>
                        <div class="brand-text-group">
                            <span class="brand-title" style="color:#fff;">Review<span style="color:var(--primary-light);">Me</span></span>
                            <span class="brand-tag">{{ __('messages.welcome_brand_tag') }}</span>
                        </div>
                    </a>
                    <p class="footer-brand-desc">
                        {{ __('messages.welcome_footer_desc') }}
                    </p>
                </div>

                <div>
                    <h4 class="footer-col-title">{{ __('messages.welcome_footer_nav') }}</h4>
                    <ul class="footer-links">
                        <li><a href="#features" class="footer-link">{{ __('messages.welcome_footer_features') }}</a></li>
                        <li><a href="#portfolio" class="footer-link">{{ __('messages.welcome_nav_portfolio') }}</a></li>
                        {{-- <li><a href="#qr-showcase" class="footer-link">{{ __('messages.welcome_footer_qr') }}</a></li> --}}
                        {{-- <li><a href="#admin-portal" class="footer-link">{{ __('messages.welcome_nav_admin') }}</a></li> --}}
                        <li><a href="#pricing" class="footer-link">{{ __('messages.welcome_footer_plans') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="footer-col-title">{{ __('messages.welcome_footer_portals') }}</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('login') }}" class="footer-link"><i class="fa-solid fa-key me-1"></i> {{ __('messages.welcome_admin_login') }}</a></li>
                        @if (Route::has('register'))
                        <li><a href="{{ route('register') }}" class="footer-link"><i class="fa-solid fa-user-plus me-1"></i> {{ __('messages.welcome_footer_register') }}</a></li>
                        @endif
                        @if (Route::has('auth.google'))
                        <li><a href="{{ route('auth.google') }}" class="footer-link"><i class="fa-brands fa-google me-1"></i> {{ __('messages.welcome_footer_google') }}</a></li>
                        @endif
                        <li><a href="#faq" class="footer-link">{{ __('messages.welcome_footer_support') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="footer-col-title">{{ __('messages.welcome_footer_platform') }}</h4>
                    <p style="font-size:0.8rem; color:#64748B;">
                        Laravel v{{ Illuminate\Foundation\Application::VERSION }} • PHP v{{ PHP_VERSION }}
                    </p>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="footer-copy">
                    {{ __('messages.welcome_footer_copy', ['year' => date('Y')]) }}
                </div>
                <div class="footer-bottom-links">
                    <a href="{{ route('login') }}" class="footer-link">{{ __('messages.welcome_footer_admin_access') }}</a>
                    <a href="#siteNav" class="footer-link">{{ __('messages.welcome_footer_top') }} <i class="fa-solid fa-arrow-up"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- =====================================================================
         QUICK LOGIN MODAL DIALOG
         ===================================================================== -->
    <div class="modal-overlay" id="quickLoginModal">
        <div class="modal-box">
            <button type="button" class="modal-close-btn" id="closeLoginModal" aria-label="{{ __('messages.welcome_modal_close') }}">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <div class="login-box-header">
                <div class="brand-icon-box" style="margin: 0 auto 0.75rem;">
                    <img src="{{ asset('review-me.png') }}" alt="" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fa-solid fa-lock\'></i>';">
                </div>
                <h4 class="login-box-title">{{ __('messages.welcome_modal_title') }}</h4>
                <p class="login-box-sub">{{ __('messages.welcome_modal_sub') }}</p>
            </div>

            <div class="login-buttons-stack">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100">
                    <i class="fa-solid fa-envelope"></i>
                    <span>{{ __('messages.welcome_login_password') }}</span>
                </a>

                @if (Route::has('auth.google'))
                <a href="{{ route('auth.google') }}" class="login-google-btn">
                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google">
                    <span>{{ __('messages.welcome_continue_google') }}</span>
                </a>
                @endif

                @if (Route::has('register'))
                <div class="login-divider">{{ __('messages.welcome_need_account') }}</div>
                <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">
                    <i class="fa-solid fa-store"></i>
                    <span>{{ __('messages.welcome_register_new_store') }}</span>
                </a>
                @endif
            </div>

            <div class="demo-credentials-card">
                <div class="demo-cred-title">
                    <i class="fa-solid fa-key"></i>
                    <span>{{ __('messages.welcome_test_accounts') }}</span>
                </div>
                <div class="demo-cred-row">
                    <span>{{ __('messages.welcome_demo_admin_short') }}</span>
                    <code>demo@gmail.test</code>
                </div>
                <div class="demo-cred-row">
                    <span>{{ __('messages.welcome_demo_pass_short') }}</span>
                    <code>123123123</code>
                </div>
            </div>
        </div>
    </div>

    <!-- =====================================================================
         INTERACTIVE JAVASCRIPT LOGIC
         ===================================================================== -->
    @php
    $welcomeI18n = [
    'managedBy' => __('messages.welcome_managed_by'),
    'planActive' => __('messages.welcome_plan_active'),
    'loginManageNamed' => __('messages.welcome_login_manage_named'),
    'designSuffix' => __('messages.welcome_qr_design_suffix'),
    ];
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const i18n = {
                !!json_encode($welcomeI18n, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!
            };

            const navToggle = document.getElementById('navToggle');
            const navLinks = document.getElementById('navLinks');
            if (navToggle && navLinks) {
                navToggle.addEventListener('click', function() {
                    const open = navLinks.classList.toggle('mobile-open');
                    navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                    const icon = navToggle.querySelector('i');
                    if (icon) {
                        icon.className = open ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
                    }
                });
            }

            // 2. Navbar Scroll Shadow Effect
            const siteNav = document.getElementById('siteNav');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 30) {
                    siteNav.classList.add('scrolled');
                } else {
                    siteNav.classList.remove('scrolled');
                }
            });

            /* 3. Perspective Switcher Logic (admin-portal section commented out)
            const perspectiveTabs = document.querySelectorAll('.perspective-tab');
            const storeNameEl = document.getElementById('perspectiveStoreName');
            const storeRoleEl = document.getElementById('perspectiveStoreRole');
            const storePlanEl = document.getElementById('perspectiveStorePlan');
            const statReviewsEl = document.getElementById('perspectiveStatReviews');
            const statRatingEl = document.getElementById('perspectiveStatRating');
            const statStaffEl = document.getElementById('perspectiveStatStaff');
            const previewQrBtn = document.getElementById('perspectivePreviewQrBtn');
            const loginBtn = document.getElementById('perspectiveLoginBtn');
            const standStoreName = document.getElementById('standStoreName');

            perspectiveTabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    perspectiveTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    const name = this.dataset.storeName;
                    const role = this.dataset.storeRole;
                    const plan = this.dataset.storePlan;
                    const staff = this.dataset.staffCount;
                    const reviews = this.dataset.reviewsCount;
                    const rating = this.dataset.rating;
                    const manager = this.dataset.manager;
                    const slug = this.dataset.slug;

                    if (storeNameEl) storeNameEl.textContent = name;
                    if (storeRoleEl) storeRoleEl.textContent = i18n.managedBy.replace(':name', manager).replace(':role', role);
                    if (storePlanEl) storePlanEl.textContent = i18n.planActive.replace(':plan', plan);
                    if (statReviewsEl) statReviewsEl.textContent = reviews;
                    if (statRatingEl) statRatingEl.textContent = rating;
                    if (statStaffEl) statStaffEl.textContent = staff;
                    if (standStoreName) standStoreName.textContent = name;

                    if (previewQrBtn && slug) {
                        previewQrBtn.href = `{{ url('/public/qr') }}/${slug}`;
                    }
                    if (loginBtn) {
                        loginBtn.innerHTML = `<i class="fa-solid fa-right-to-bracket"></i><span>${i18n.loginManageNamed.replace(':name', name)}</span>`;
                    }
                });
            });
            */

            /* 4. QR Template Styles Switcher Logic (qr-showcase section commented out)
            const qrStyleOptions = document.querySelectorAll('.qr-style-option');
            const standStyleLabel = document.getElementById('standStyleLabel');
            const standQrBox = document.getElementById('standQrBox');

            const styleBorders = {
                classic: { border: '3px solid #6868AC', shadow: '0 4px 15px rgba(104,104,172,0.2)' },
                neon: { border: '3px solid #06B6D4', shadow: '0 0 20px rgba(6,182,212,0.5)' },
                royal: { border: '3px solid #F59E0B', shadow: '0 0 20px rgba(245,158,11,0.4)' },
                dotted: { border: '3px dashed #10B981', shadow: '0 4px 15px rgba(16,185,129,0.3)' },
                sunburst: { border: '3px solid #EC4899', shadow: '0 0 20px rgba(236,72,153,0.4)' },
                tech: { border: '3px solid #141655', shadow: '0 4px 20px rgba(20,22,85,0.4)' }
            };

            qrStyleOptions.forEach(opt => {
                opt.addEventListener('click', function () {
                    qrStyleOptions.forEach(o => o.classList.remove('active'));
                    this.classList.add('active');

                    const styleKey = this.dataset.style;
                    const name = this.dataset.name;

                    if (standStyleLabel) {
                        standStyleLabel.textContent = i18n.designSuffix.replace(':name', name);
                    }
                    if (standQrBox && styleBorders[styleKey]) {
                        standQrBox.style.border = styleBorders[styleKey].border;
                        standQrBox.style.boxShadow = styleBorders[styleKey].shadow;
                    }
                });
            });
            */

            // 5. FAQ Accordion Toggle
            const faqItems = document.querySelectorAll('.faq-item');
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');
                question.addEventListener('click', () => {
                    const isOpen = item.classList.contains('open');
                    faqItems.forEach(i => i.classList.remove('open'));
                    if (!isOpen) {
                        item.classList.add('open');
                    }
                });
            });

            // 6. Quick Login Modal Open/Close
            const loginModal = document.getElementById('quickLoginModal');
            const openBtns = document.querySelectorAll('.js-open-login-modal');
            const closeBtn = document.getElementById('closeLoginModal');

            openBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (loginModal) loginModal.classList.add('open');
                });
            });

            if (closeBtn && loginModal) {
                closeBtn.addEventListener('click', () => {
                    loginModal.classList.remove('open');
                });
            }

            if (loginModal) {
                loginModal.addEventListener('click', (e) => {
                    if (e.target === loginModal) {
                        loginModal.classList.remove('open');
                    }
                });
            }
        });
    </script>
</body>

</html>