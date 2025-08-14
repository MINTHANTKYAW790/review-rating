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
            <h5>Store Profile Management</h5>
            <small class="text-muted" style="font-size: 12px;">Update your store information and settings</small>
        </div>
        @elseif (request()->routeIs('staff*'))
        <div class="d-flex flex-column align-items-start">
            <h5>Staff Management</h5>
            <small class="text-muted" style="font-size: 12px;">Manage your team members and their information</small>
        </div>
        @elseif (request()->routeIs('public*'))
        <div class="d-flex flex-column align-items-start">
            <h5>QR Code Generator</h5>
            <small class="text-muted" style="font-size: 12px;">Generate QR codes for customer reviews</small>
        </div>
        @elseif (request()->routeIs('review*'))
        <div class="d-flex flex-column align-items-start">
            <h5>Reviews & Ratings Management </h5>
            <small class="text-muted" style="font-size: 12px;">Manage customer reviews and ratings  </small>
        </div>
        @endif
        <li class="nav-item">
            @if (auth('web')->check())
            <i class="fas fa-user-circle"></i> {{ auth('web')->user()->name }}
            @else
            <i class="fas fa-user-circle"></i> Guest
            @endif
        </li>
    </ul>
</nav>