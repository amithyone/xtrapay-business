<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center w-100">
            <!-- Logo -->
            <div class="d-none d-lg-block">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('build/assets/logo.svg') }}" alt="Xtrapay Business Logo" style="height: 40px; width: auto;" />
                </a>
            </div>

            <!-- Mobile Logo -->
            <div class="d-lg-none text-center">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('build/assets/logo.svg') }}" alt="Xtrapay Business Logo" style="height: 40px; width: auto;" />
                </a>
            </div>

            <!-- Hamburger -->
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </div>

    <!-- Offcanvas Sidebar -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Xtrapay Business</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav text-center">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        {{ __('Dashboard') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('sites.index') }}" class="nav-link {{ request()->routeIs('sites.*') ? 'active' : '' }}">
                        {{ __('Sites') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                        {{ __('Transactions') }}
                    </a>
                </li>
                @if(auth()->check() && auth()->user()->isSuperAdmin())
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('super-admin*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Super Admin
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('super-admin.dashboard') }}">Dashboard</a></li>
                        <li><a class="dropdown-item" href="{{ route('super-admin.users.index') }}">Users</a></li>
                        <li><a class="dropdown-item" href="{{ route('super-admin.businesses.index') }}">Businesses</a></li>
                        <li><a class="dropdown-item" href="{{ route('super-admin.withdrawals.index') }}">Withdrawals</a></li>
                        <li><a class="dropdown-item" href="{{ route('super-admin.savings.index') }}">Savings</a></li>
                        <li><a class="dropdown-item" href="{{ route('super-admin.tickets.index') }}">Tickets</a></li>
                        <li><a class="dropdown-item" href="{{ route('super-admin.reports.index') }}">Reports</a></li>
                    </ul>
                </li>
                @endif
            </ul>

            <!-- User Settings -->
            @auth
            <div class="mt-3 d-flex align-items-center justify-content-center">
                <span class="me-2">{{ Auth::user()->name }}</span>
                <a href="{{ route('profile.edit') }}" class="text-dark me-3" title="Profile">
                    <i class="fas fa-user-circle"></i>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link text-dark p-0" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
            @endauth
        </div>
    </div>
</nav> 