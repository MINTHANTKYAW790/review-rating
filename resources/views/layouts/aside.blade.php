<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ url('/') }}" class="brand-link">
        <img src="{{ asset('images/favicon.ico')}}" alt="" class="brand-image elevation-3" style="opacity: 1">
        <span class="brand-text font-weight-light d-flex flex-row align-items-center">
            <i class="fas fa-store ml-2 mr-4"></i>
            <div class="d-flex flex-column align-items-start">
                <div>{{ isset($store) && !empty($store->name) ? $store->name : __('messages.store_admin') }}</div>
                <small class="text-muted" style="font-size: 12px;">{{ __('messages.management_panel_small') }}</small>
            </div>
        </span>
    </a>
    <div class="sidebar">
        <div class="form-inline mt-2">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sm form-control-sidebar" type="search" placeholder="{{ __('messages.search') }}" aria-label="{{ __('messages.search') }}">
                <div class="input-group-append">
                    <button class="btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link @if (Request::is('dashboard*')) active @endif">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        <p>
                            {{ __('messages.dashboard') }}
                        </p>
                    </a>
                </li>
            </ul>
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('store.index') }}" class="nav-link @if (Request::is('store*')) active @endif">
                        <i class="fas fa-store nav-icon"></i>
                        <p>
                            {{ __('messages.store_profile') }}
                        </p>
                    </a>
                </li>
            </ul>
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('staff.index') }}" class="nav-link @if (Request::is('staff*')) active @endif">
                        <i class="fas fa-users nav-icon"></i>
                        <p>
                            {{ __('messages.staff_management') }}
                        </p>
                    </a>
                </li>
            </ul>

            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('public.qr-preview') }}" class="nav-link @if (Request::is('public*')) active @endif">
                        <i class="fas fa-qrcode nav-icon"></i>
                        <p>
                            {{ __('messages.qr_code_generator') }}
                        </p>
                    </a>
                </li>
            </ul>
            
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('review.index') }}" class="nav-link @if (Request::is('review*')) active @endif">
                        <i class="far fa-star nav-icon"></i>
                        <p>
                            {{ __('messages.reviews_ratings') }}
                        </p>
                    </a>
                </li>
            </ul>
            
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link btn btn-link text-left" style="color: #c2c7d0; padding: 0;">
                            <i class="fas fa-sign-out-alt nav-icon"></i>
                            <p>{{ __('messages.logout') }}</p>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>