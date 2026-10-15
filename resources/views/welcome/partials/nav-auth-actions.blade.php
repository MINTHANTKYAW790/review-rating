@php
    $idSuffix = ($placement ?? 'bar') === 'drawer' ? 'Drawer' : 'Bar';
@endphp
@auth
    @php
        $targetRoute = auth()->user()->isSuperAdmin()
            ? route('superadmin.dashboard')
            : route('dashboard');
        $userStoreCount = $userStores ? $userStores->count() : 0;
    @endphp
    <a href="{{ $targetRoute }}" class="btn btn-outline-primary btn-sm">
        <i class="fa-solid fa-shop"></i>
        <span>{{ $userStoreCount > 0 ? __('messages.welcome_stores_count', ['count' => $userStoreCount]) : __('messages.welcome_dashboard') }}</span>
    </a>
    <a href="{{ $targetRoute }}" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-gauge-high"></i>
        <span>{{ __('messages.welcome_admin_portal') }}</span>
    </a>
    <form method="POST" action="{{ route('logout') }}" class="nav-logout-form">
        @csrf
        <button type="submit" class="btn btn-ghost btn-sm" title="{{ __('messages.welcome_logout') }}">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            <span class="nav-logout-label">{{ __('messages.welcome_logout') }}</span>
        </button>
    </form>
@else
    <a href="{{ route('login') }}" class="btn btn-primary btn-sm" id="navAdminLoginBtn{{ $idSuffix }}">
        <i class="fa-solid fa-right-to-bracket"></i>
        <span>{{ __('messages.welcome_login') }}</span>
    </a>
    @if (Route::has('register'))
        <a href="{{ route('register') }}" class="btn btn-gold btn-sm">
            <span>{{ __('messages.welcome_get_started_free') }}</span>
        </a>
    @endif
@endauth
