<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item ml-1">
            @if (View::hasSection('nav-data'))
            <span class="navbar-brand" href="javascript:void(0)">{{ View::getSection('nav-data') }}</span>
            @endif
        </li>
    </ul>

    <ul class="navbar-nav ml-auto w-100 d-flex flex-row align-items-center justify-content-between">
        @if (request()->routeIs('store*'))
        <div class="d-flex flex-column align-items-start">
            <h5>{{ __('messages.store_profile_management') }}</h5>
            <small class="text-muted" style="font-size: 12px;">{{ __('messages.update_store_info_settings') }}</small>
        </div>
        @elseif (request()->routeIs('staff*'))
        <div class="d-flex flex-column align-items-start">
            <h5>{{ __('messages.staff_management') }}</h5>
            <small class="text-muted" style="font-size: 12px;">{{ __('messages.manage_team_info') }}</small>
        </div>
        @elseif (request()->routeIs('public*'))
        <div class="d-flex flex-column align-items-start">
            <h5>{{ __('messages.qr_code_generator') }}</h5>
            <small class="text-muted" style="font-size: 12px;">{{ __('messages.generate_qr_customer_reviews') }}</small>
        </div>
        @elseif (request()->routeIs('review*'))
        <div class="d-flex flex-column align-items-start">
            <h5>{{ __('messages.reviews_ratings_management') }} </h5>
            <small class="text-muted" style="font-size: 12px;">{{ __('messages.manage_customer_reviews_ratings') }}  </small>
        </div>
        @endif
        <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-globe"></i> {{ strtoupper(app()->getLocale()) }}
    </a>
    <div class="dropdown-menu" aria-labelledby="languageDropdown">
        <a class="dropdown-item" href="{{ route('language.switch', 'en') }}">{{ __('messages.english') }}</a>
        <a class="dropdown-item" href="{{ route('language.switch', 'my') }}">{{ __('messages.burmese') }}</a>
    </div>
</li>
<li class="nav-item">
    <button id="theme-toggle" class="btn btn-link nav-link">
        <i class="fas fa-moon"></i>
    </button>
</li>
        <li class="nav-item d-flex align-items-center">
            @if (auth('web')->check())
                <i class="fas fa-user-circle me-1"></i> {{ auth('web')->user()->name }}
            @else
                <i class="fas fa-user-circle"></i> {{ __('messages.guest') }}
            @endif
        </li>
    </ul>
</nav>