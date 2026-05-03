<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @php
        $user = Auth::user();
        $isAdmin = $user?->isAdmin() ?? false;
        $activeAdminPanel = request()->query('panel', 'analytics');

        if ($activeAdminPanel === 'dashboard') {
            $activeAdminPanel = 'analytics';
        }

        if ($activeAdminPanel === 'courts') {
            $activeAdminPanel = 'rates';
        }

        $adminPanelTitles = [
            'analytics' => 'Dashboard',
            'monitor' => 'Booking Monitor',
            'booking' => 'Walk-in Booking',
            'income' => 'Income Report',
            'rates' => 'Rates & Courts',
        ];

        $pageTitle = $isAdmin ? ($adminPanelTitles[$activeAdminPanel] ?? 'Admin Dashboard') : 'My Dashboard';
        $roleLabel = $isAdmin ? 'Administrator' : ($user?->hasRole('customer') ? 'Customer' : 'Member');
        $adminRouteIsActive = request()->routeIs('admin.dashboard', 'admin') || ($isAdmin && request()->routeIs('dashboard'));

        $userInitials = collect(explode(' ', trim((string) $user?->name)))
            ->filter()
            ->map(fn (string $part) => strtoupper(substr($part, 0, 1)))
            ->take(2)
            ->implode('');

        $adminNavGroups = [
            [
                'label' => 'Operations',
                'items' => [
                    [
                        'label' => 'Dashboard',
                        'description' => 'Analytics overview',
                        'icon' => 'fas fa-chart-line',
                        'href' => route('admin.dashboard', ['panel' => 'analytics']),
                        'active' => $adminRouteIsActive && $activeAdminPanel === 'analytics',
                    ],
                    [
                        'label' => 'Booking Monitor',
                        'description' => 'Search and manage reservations',
                        'icon' => 'fas fa-calendar-check',
                        'href' => route('admin.dashboard', ['panel' => 'monitor']),
                        'active' => $adminRouteIsActive && $activeAdminPanel === 'monitor',
                    ],
                    [
                        'label' => 'Book Now',
                        'description' => 'Walk-in and assisted bookings',
                        'icon' => 'fas fa-cash-register',
                        'href' => route('admin.dashboard', ['panel' => 'booking']),
                        'active' => $adminRouteIsActive && $activeAdminPanel === 'booking',
                    ],
                    [
                        'label' => 'Income Report',
                        'description' => 'Revenue and daily range',
                        'icon' => 'fas fa-wallet',
                        'href' => route('admin.dashboard', ['panel' => 'income']),
                        'active' => $adminRouteIsActive && $activeAdminPanel === 'income',
                    ],
                    [
                        'label' => 'Rates & Courts',
                        'description' => 'Court setup and rentals',
                        'icon' => 'fas fa-sliders-h',
                        'href' => route('admin.dashboard', ['panel' => 'rates']),
                        'active' => $adminRouteIsActive && $activeAdminPanel === 'rates',
                    ],
                ],
            ],
            [
                'label' => 'Public Tools',
                'items' => [
                    [
                        'label' => 'Landing & Booking',
                        'description' => 'Customer-facing page',
                        'icon' => 'fas fa-globe',
                        'href' => route('reservations.index'),
                        'active' => request()->routeIs('reservations.index'),
                    ],
                    [
                        'label' => 'Verify Receipt',
                        'description' => 'Public receipt checker',
                        'icon' => 'fas fa-receipt',
                        'href' => route('reservations.receipt.verify'),
                        'active' => request()->routeIs('reservations.receipt.verify'),
                    ],
                ],
            ],
        ];

        $customerNavGroups = [
            [
                'label' => 'Player Portal',
                'items' => [
                    [
                        'label' => 'My Dashboard',
                        'description' => 'Bookings and receipts',
                        'icon' => 'fas fa-home',
                        'href' => route('dashboard'),
                        'active' => request()->routeIs('dashboard'),
                    ],
                    [
                        'label' => 'Book a Court',
                        'description' => 'Reserve an available slot',
                        'icon' => 'fas fa-calendar-plus',
                        'href' => route('reservations.index') . '#booking-section',
                        'active' => request()->routeIs('reservations.index'),
                    ],
                    [
                        'label' => 'My Reservations',
                        'description' => 'Reservation history',
                        'icon' => 'fas fa-list-ul',
                        'href' => route('dashboard') . '#my-reservations',
                        'active' => false,
                    ],
                    [
                        'label' => 'Rain Reschedule',
                        'description' => 'Unlocked schedule changes',
                        'icon' => 'fas fa-cloud-showers-heavy',
                        'href' => route('dashboard') . '#rain-reschedule',
                        'active' => false,
                    ],
                    [
                        'label' => 'Verify Receipt',
                        'description' => 'Check a receipt code',
                        'icon' => 'fas fa-receipt',
                        'href' => route('reservations.receipt.verify'),
                        'active' => request()->routeIs('reservations.receipt.verify'),
                    ],
                ],
            ],
        ];

        $accountGroup = [
            'label' => 'Account Pages',
            'items' => [
                [
                    'label' => 'Profile',
                    'description' => 'Name, email, password',
                    'icon' => 'fas fa-user-circle',
                    'href' => route('profile.edit'),
                    'active' => request()->routeIs('profile.*'),
                ],
                [
                    'label' => 'Log Out',
                    'description' => 'End this session',
                    'icon' => 'fas fa-sign-out-alt',
                    'form' => true,
                ],
            ],
        ];

        $navGroups = $isAdmin ? $adminNavGroups : $customerNavGroups;
        $navGroups[] = $accountGroup;

        $homeRoute = $isAdmin ? route('admin.dashboard') : route('dashboard');
        $topActionHref = $isAdmin ? route('reservations.index') : route('reservations.index') . '#booking-section';
        $topActionLabel = $isAdmin ? 'Public Page' : 'Book Court';
        $topActionIcon = $isAdmin ? 'fas fa-globe' : 'fas fa-calendar-plus';
    @endphp

    <head>
        @include('layouts.soft-ui-head', ['title' => $pageTitle . ' - ' . config('app.name', 'Pickle BALLan Ni Juan')])
    </head>

    <body class="g-sidenav-show bg-gray-100 soft-dashboard-page">
        <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3" id="sidenav-main">
            <div class="sidenav-header">
                <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
                <a class="navbar-brand m-0" href="{{ $homeRoute }}">
                    <img src="{{ asset('soft-ui-dashboard-main/assets/img/logo-ct-dark.png') }}" class="navbar-brand-img h-100" alt="main_logo">
                    <span class="ms-1 font-weight-bold">Pickle BALLan</span>
                </a>
            </div>

            <hr class="horizontal dark mt-0">

            <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
                <ul class="navbar-nav">
                    @foreach($navGroups as $group)
                        @unless($loop->first)
                            <li class="nav-item mt-3">
                                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">{{ $group['label'] }}</h6>
                            </li>
                        @endunless

                        @foreach($group['items'] as $item)
                            <li class="nav-item">
                                @if($item['form'] ?? false)
                                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="nav-link soft-sidebar-button border-0 bg-transparent w-100 text-start">
                                            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                                <i class="{{ $item['icon'] }} text-dark text-sm opacity-10"></i>
                                            </div>
                                            <span class="nav-link-text ms-1">{{ $item['label'] }}</span>
                                        </button>
                                    </form>
                                @else
                                    <a class="nav-link {{ $item['active'] ? 'active' : '' }}" href="{{ $item['href'] }}">
                                        <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                                            <i class="{{ $item['icon'] }} text-dark text-sm opacity-10"></i>
                                        </div>
                                        <span class="nav-link-text ms-1">{{ $item['label'] }}</span>
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    @endforeach
                </ul>
            </div>

            <div class="sidenav-footer mx-3">
                <div class="card card-background shadow-none card-background-mask-secondary" id="sidenavCard">
                    <div class="full-background" style="background-image: url('{{ asset('soft-ui-dashboard-main/assets/img/curved-images/white-curved.jpg') }}')"></div>
                    <div class="card-body text-start p-3 w-100">
                        <div class="icon icon-shape icon-sm bg-white shadow text-center mb-3 d-flex align-items-center justify-content-center">
                            <i class="{{ $topActionIcon }} text-dark text-sm opacity-10"></i>
                        </div>
                        <div class="docs-info">
                            <h6 class="text-white up mb-0">{{ $roleLabel }}</h6>
                            <p class="text-xs font-weight-bold">{{ $user?->name }}</p>
                            <a href="{{ $topActionHref }}" class="btn btn-white btn-sm w-100 mb-0">{{ $topActionLabel }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
            <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
                <div class="container-fluid py-1 px-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                            <li class="breadcrumb-item text-sm">
                                <a class="opacity-5 text-dark" href="{{ route('reservations.index') }}">Pages</a>
                            </li>
                            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">{{ $pageTitle }}</li>
                        </ol>
                        <h6 class="font-weight-bolder mb-0">{{ $pageTitle }}</h6>
                    </nav>

                    <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                        <form class="ms-md-auto pe-md-3 d-flex align-items-center" method="GET" action="{{ $isAdmin ? route('admin.dashboard') : route('reservations.index') }}">
                            @if($isAdmin)
                                <input type="hidden" name="panel" value="monitor">
                            @endif
                            <div class="input-group">
                                <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="{{ $isAdmin ? 'customer' : 'q' }}"
                                    placeholder="{{ $isAdmin ? 'Search bookings...' : 'Type here...' }}"
                                    aria-label="Search"
                                >
                            </div>
                        </form>

                        <ul class="navbar-nav justify-content-end">
                            <li class="nav-item d-flex align-items-center">
                                <a class="btn btn-outline-primary btn-sm mb-0 me-3" href="{{ $topActionHref }}">
                                    {{ $topActionLabel }}
                                </a>
                            </li>
                            <li class="nav-item d-flex align-items-center">
                                <a href="{{ route('profile.edit') }}" class="nav-link text-body font-weight-bold px-0">
                                    <i class="fa fa-user me-sm-1"></i>
                                    <span class="d-sm-inline d-none">{{ $user?->name }}</span>
                                </a>
                            </li>
                            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                                <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav" aria-label="Open sidebar">
                                    <div class="sidenav-toggler-inner">
                                        <i class="sidenav-toggler-line"></i>
                                        <i class="sidenav-toggler-line"></i>
                                        <i class="sidenav-toggler-line"></i>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item px-3 d-flex align-items-center">
                                <a href="{{ route('profile.edit') }}" class="nav-link text-body p-0" aria-label="Profile settings">
                                    <i class="fa fa-cog cursor-pointer"></i>
                                </a>
                            </li>
                            <li class="nav-item dropdown pe-2 d-flex align-items-center">
                                <a href="javascript:;" class="nav-link text-body p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-bell cursor-pointer"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
                                    <li class="mb-2">
                                        <a class="dropdown-item border-radius-md" href="{{ $topActionHref }}">
                                            <div class="d-flex py-1">
                                                <div class="my-auto">
                                                    <span class="avatar avatar-sm bg-gradient-primary me-3 d-flex align-items-center justify-content-center">
                                                        <i class="{{ $topActionIcon }} text-white text-xs"></i>
                                                    </span>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="text-sm font-weight-normal mb-1">
                                                        <span class="font-weight-bold">{{ $topActionLabel }}</span>
                                                    </h6>
                                                    <p class="text-xs text-secondary mb-0">
                                                        <i class="fa fa-clock me-1"></i>
                                                        Ready now
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <div class="container-fluid py-4 soft-dashboard-content">
                @isset($header)
                    {{ $header }}
                @endisset

                {{ $slot }}
            </div>
        </main>

        @include('layouts.soft-ui-scripts')
    </body>
</html>
