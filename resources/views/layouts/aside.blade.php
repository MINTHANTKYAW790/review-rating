<aside class="main-sidebar sidebar-dark-primary elevation-2">
    <a href="{{ auth()->user()?->isSuperAdmin() ? route('superadmin.dashboard') : route('dashboard') }}" class="brand-link">
        <img src="{{ asset('images/favicon.ico') }}" alt="" class="brand-image" style="opacity: 1">
        <span class="brand-text font-weight-light">
            <span class="brand-title">{{ auth()->user()?->isSuperAdmin() ? 'Superadmin' : (isset($store) && !empty($store->name) ? $store->name : __('messages.store_admin')) }}</span>
            <small class="brand-subtitle d-block">{{ __('messages.management_panel_small') }}</small>
        </span>
    </a>
    <div class="sidebar">
        <nav class="mt-3">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @if (auth()->user()?->isSuperAdmin())
                    <li class="nav-item">
                        <a href="{{ route('superadmin.dashboard') }}" class="nav-link @if (request()->routeIs('superadmin.dashboard')) active @endif">
                            <i class="fas fa-tachometer-alt nav-icon"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('superadmin.users.index') }}" class="nav-link @if (request()->routeIs('superadmin.users*')) active @endif">
                            <i class="fas fa-users-cog nav-icon"></i>
                            <p>Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('superadmin.stores.index') }}" class="nav-link @if (request()->routeIs('superadmin.stores*')) active @endif">
                            <i class="fas fa-store nav-icon"></i>
                            <p>Stores</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('roles.index') }}" class="nav-link @if (Request::is('roles*')) active @endif">
                            <i class="fas fa-user-tag nav-icon"></i>
                            <p>Roles</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('superadmin.plans.index') }}" class="nav-link @if (request()->routeIs('superadmin.plans*')) active @endif">
                            <i class="fas fa-tags nav-icon"></i>
                            <p>Plans</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('superadmin.payments.index') }}" class="nav-link @if (request()->routeIs('superadmin.payments*')) active @endif">
                            <i class="fas fa-file-invoice-dollar nav-icon"></i>
                            <p>Payments</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('superadmin.subscriptions.index') }}" class="nav-link @if (request()->routeIs('superadmin.subscriptions*')) active @endif">
                            <i class="fas fa-receipt nav-icon"></i>
                            <p>Subscriptions</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('superadmin.billing-settings.edit') }}" class="nav-link @if (request()->routeIs('superadmin.billing-settings*')) active @endif">
                            <i class="fas fa-university nav-icon"></i>
                            <p>Billing settings</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('activity-log.index') }}" class="nav-link @if (Request::is('activity-log*')) active @endif">
                            <i class="fas fa-history nav-icon"></i>
                            <p>Activity Log</p>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link @if (Request::is('dashboard*')) active @endif">
                            <i class="fas fa-tachometer-alt nav-icon"></i>
                            <p>{{ __('messages.dashboard') }}</p>
                        </a>
                    </li>

                    @can('view_users')
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}" class="nav-link @if (Request::is('users*')) active @endif">
                            <i class="fas fa-users-cog nav-icon"></i>
                            <p>Users</p>
                        </a>
                    </li>
                    @endcan

                    @can('view_stores')
                    <li class="nav-item">
                        <a href="{{ route('store.index') }}" class="nav-link @if (Request::is('store*')) active @endif">
                            <i class="fas fa-store nav-icon"></i>
                            <p>{{ __('messages.store_profile') }}</p>
                        </a>
                    </li>
                    @endcan

                    @can('view_staff')
                    <li class="nav-item">
                        <a href="{{ route('staff.index') }}" class="nav-link @if (Request::is('staff*')) active @endif">
                            <i class="fas fa-users nav-icon"></i>
                            <p>{{ __('messages.staff_management') }}</p>
                        </a>
                    </li>
                    @endcan

                    @can('view_stores')
                    <li class="nav-item">
                        <a href="{{ route('public.qr-preview') }}" class="nav-link @if (Request::is('public/qr/qr-preview*') || Request::is('qr*')) active @endif">
                            <i class="fas fa-qrcode nav-icon"></i>
                            <p>{{ __('messages.qr_code_generator') }}</p>
                        </a>
                    </li>
                    @endcan

                    @can('view_reviews')
                    <li class="nav-item">
                        <a href="{{ route('review.index') }}" class="nav-link @if (Request::is('review*')) active @endif">
                            <i class="far fa-star nav-icon"></i>
                            <p>{{ __('messages.reviews_ratings') }}</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('notes.index') }}" class="nav-link @if (Request::is('notes*')) active @endif">
                            <i class="far fa-sticky-note nav-icon"></i>
                            <p>{{ __('messages.store_notes') }}</p>
                        </a>
                    </li>
                    @endcan

                    @can('buy_plans')
                    <li class="nav-item has-treeview {{ request()->routeIs('billing*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('billing*') ? 'active' : '' }}">
                            <i class="fas fa-credit-card nav-icon"></i>
                            <p>
                                {{ __('messages.buy_plans') }}
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('billing.add-store') }}" class="nav-link @if (request()->routeIs('billing.add-store')) active @endif">
                                    <i class="far fa-plus-square nav-icon"></i>
                                    <p>{{ __('messages.add_one_store_card') }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('billing.extend') }}" class="nav-link @if (request()->routeIs('billing.extend')) active @endif">
                                    <i class="far fa-calendar-plus nav-icon"></i>
                                    <p>{{ __('messages.extend_stores_card') }}</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('billing.payments') }}" class="nav-link @if (request()->routeIs('billing.payments') || request()->routeIs('billing.show')) active @endif">
                                    <i class="far fa-list-alt nav-icon"></i>
                                    <p>{{ __('messages.payment_history') }}</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endcan
                @endif

                <li class="nav-item mt-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link btn btn-link text-left logout-nav-btn">
                            <i class="fas fa-sign-out-alt nav-icon"></i>
                            <p>{{ __('messages.logout') }}</p>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>
