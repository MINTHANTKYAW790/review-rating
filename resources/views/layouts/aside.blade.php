<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ url('/') }}" class="brand-link">
        <img src="{{ asset('images/favicon.ico')}}" alt="" class="brand-image elevation-3" style="opacity: 1">
        <span class="brand-text font-weight-light d-flex flex-row align-items-center">
            <i class="fas fa-store ml-2 mr-4"></i>
            <div class="d-flex flex-column align-items-start">
                <div>{{ isset($store) && !empty($store->name) ? $store->name : 'Store Admin' }}</div>
                <small class="text-muted" style="font-size: 12px;">management panel</small>
            </div>
        </span>
    </a>
    <div class="sidebar">
        <div class="form-inline mt-2">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sm form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>
        <nav class="mt-2">
            <!-- <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('store.create') }}" class="nav-link @if (Request::is('store*')) active @endif">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
            </ul> -->
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('store.index') }}" class="nav-link @if (Request::is('store*')) active @endif">
                        <i class="fas fa-store nav-icon"></i>
                        <p>
                            Store Profile
                        </p>
                    </a>
                </li>
            </ul>
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('staff.index') }}" class="nav-link @if (Request::is('staff*')) active @endif">
                        <i class="fas fa-users nav-icon"></i>
                        <p>
                            Staff Management
                        </p>
                    </a>
                </li>
            </ul>

            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('public.qr-preview') }}" class="nav-link @if (Request::is('public*')) active @endif">
                        <i class="fas fa-qrcode nav-icon"></i>
                        <p>
                            QR Code Generator
                        </p>
                    </a>
                </li>
            </ul>
            
            <!-- <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('store.create') }}" class="nav-link @if (Request::is('store*')) active @endif">
                        <i class="far fa-circle nav-icon"></i>
                        <p>
                            Reviews & Ratings
                        </p>
                    </a>
                </li>
            </ul>
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" class="nav-link">
                        @csrf
                        <a hrefs="#" class="logout-btn" onclick="event.preventDefault(); this.closest('form').submit();" style="cursor: pointer;">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>
                                Logout
                            </p>
                        </a>
                    </form>
                </li>
            </ul> -->
        </nav>
    </div>
</aside>