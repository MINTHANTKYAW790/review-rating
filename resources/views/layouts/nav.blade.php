<nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-md-flex align-items-center ml-2">
            @if (request()->routeIs('superadmin.dashboard'))
                <div class="nav-page-meta">
                    <span class="nav-page-title">Superadmin</span>
                    <small class="nav-page-subtitle">Platform overview</small>
                </div>
            @elseif (request()->routeIs('superadmin.users*'))
                <div class="nav-page-meta">
                    <span class="nav-page-title">Users</span>
                    <small class="nav-page-subtitle">Accounts and their stores</small>
                </div>
            @elseif (request()->routeIs('superadmin.stores*'))
                <div class="nav-page-meta">
                    <span class="nav-page-title">Stores</span>
                    <small class="nav-page-subtitle">Staff and recent reviews</small>
                </div>
            @elseif (request()->routeIs('dashboard'))
                <div class="nav-page-meta">
                    <span class="nav-page-title">{{ __('messages.dashboard') }}</span>
                </div>
            @elseif (request()->routeIs('store*'))
                <div class="nav-page-meta">
                    <span class="nav-page-title">{{ __('messages.store_profile_management') }}</span>
                    <small class="nav-page-subtitle">{{ __('messages.update_store_info_settings') }}</small>
                </div>
            @elseif (request()->routeIs('staff*'))
                <div class="nav-page-meta">
                    <span class="nav-page-title">{{ __('messages.staff_management') }}</span>
                    <small class="nav-page-subtitle">{{ __('messages.manage_team_info') }}</small>
                </div>
            @elseif (request()->routeIs('public*') || request()->routeIs('qr*'))
                <div class="nav-page-meta">
                    <span class="nav-page-title">{{ __('messages.qr_code_generator') }}</span>
                    <small class="nav-page-subtitle">{{ __('messages.generate_qr_customer_reviews') }}</small>
                </div>
            @elseif (request()->routeIs('review*'))
                <div class="nav-page-meta">
                    <span class="nav-page-title">{{ __('messages.reviews_ratings_management') }}</span>
                    <small class="nav-page-subtitle">{{ __('messages.manage_customer_reviews_ratings') }}</small>
                </div>
            @elseif (request()->routeIs('users*'))
                <div class="nav-page-meta">
                    <span class="nav-page-title">Users</span>
                    <small class="nav-page-subtitle">Manage accounts and roles</small>
                </div>
            @elseif (request()->routeIs('roles*'))
                <div class="nav-page-meta">
                    <span class="nav-page-title">Roles</span>
                    <small class="nav-page-subtitle">Permissions and access</small>
                </div>
            @elseif (request()->routeIs('activity-log*'))
                <div class="nav-page-meta">
                    <span class="nav-page-title">Activity Log</span>
                    <small class="nav-page-subtitle">Recent admin changes</small>
                </div>
            @elseif (request()->routeIs('superadmin.plans*'))
                <div class="nav-page-meta">
                    <span class="nav-page-title">Plans</span>
                    <small class="nav-page-subtitle">Catalog and limits</small>
                </div>
            @elseif (request()->routeIs('superadmin.payments*'))
                <div class="nav-page-meta">
                    <span class="nav-page-title">Payments</span>
                    <small class="nav-page-subtitle">Review owner transfers</small>
                </div>
            @elseif (request()->routeIs('superadmin.subscriptions*'))
                <div class="nav-page-meta">
                    <span class="nav-page-title">Subscriptions</span>
                    <small class="nav-page-subtitle">Store plan periods</small>
                </div>
            @elseif (request()->routeIs('superadmin.billing-settings*'))
                <div class="nav-page-meta">
                    <span class="nav-page-title">Billing settings</span>
                    <small class="nav-page-subtitle">KBZ Pay and Wave numbers</small>
                </div>
            @elseif (request()->routeIs('billing.add-store'))
                <div class="nav-page-meta">
                    <span class="nav-page-title">{{ __('messages.add_one_store_card') }}</span>
                    <small class="nav-page-subtitle">{{ __('messages.add_one_store_description') }}</small>
                </div>
            @elseif (request()->routeIs('billing.extend'))
                <div class="nav-page-meta">
                    <span class="nav-page-title">{{ __('messages.extend_stores_card') }}</span>
                    <small class="nav-page-subtitle">{{ __('messages.extend_stores_subtitle') }}</small>
                </div>
            @elseif (request()->routeIs('billing.payments') || request()->routeIs('billing.show'))
                <div class="nav-page-meta">
                    <span class="nav-page-title">{{ __('messages.payment_history') }}</span>
                    <small class="nav-page-subtitle">{{ __('messages.payment_history_subtitle') }}</small>
                </div>
            @elseif (request()->routeIs('billing*'))
                <div class="nav-page-meta">
                    <span class="nav-page-title">{{ __('messages.buy_plans') }}</span>
                    <small class="nav-page-subtitle">{{ __('messages.buy_plans_subtitle') }}</small>
                </div>
            @endif
        </li>
    </ul>

    <ul class="navbar-nav ml-auto align-items-center">
        @if (! empty($headerPlanCue))
            @php
                $cue = $headerPlanCue;
                $cueHint = $cue['mode'] === 'free'
                    ? __('messages.header_upgrade_hint', ['store' => $cue['storeName']])
                    : __('messages.header_extend_hint', ['store' => $cue['storeName']]);
                $cueValue = $cue['mode'] === 'free'
                    ? __('messages.header_reviews_used', ['used' => $cue['used'], 'max' => $cue['max']])
                    : ($cue['daysLeft'] === 0
                        ? __('messages.header_ends_today')
                        : __('messages.header_days_left', ['days' => $cue['daysLeft']]));
            @endphp
            <li class="nav-item">
                @if ($cue['href'])
                    <a href="{{ $cue['href'] }}" class="header-plan-cue is-{{ $cue['tone'] }}" title="{{ $cueHint }}">
                        @include('layouts.partials.header-plan-cue-inner', ['cue' => $cue, 'cueValue' => $cueValue])
                    </a>
                @else
                    <span class="header-plan-cue is-{{ $cue['tone'] }} is-static" title="{{ $cueHint }}">
                        @include('layouts.partials.header-plan-cue-inner', ['cue' => $cue, 'cueValue' => $cueValue])
                    </span>
                @endif
            </li>
        @endif
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-globe"></i> {{ strtoupper(app()->getLocale()) }}
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="languageDropdown">
                <a class="dropdown-item" href="{{ route('language.switch', 'en') }}">{{ __('messages.english') }}</a>
                <a class="dropdown-item" href="{{ route('language.switch', 'my') }}">{{ __('messages.burmese') }}</a>
            </div>
        </li>
        <li class="nav-item">
            <button id="theme-toggle" class="btn btn-link nav-link" type="button" aria-label="Toggle theme">
                <i class="fas fa-moon"></i>
            </button>
        </li>
        <li class="nav-item nav-user">
            <i class="fas fa-user-circle mr-1"></i>
            <span>{{ auth('web')->check() ? auth('web')->user()->name : __('messages.guest') }}</span>
        </li>
    </ul>
</nav>
