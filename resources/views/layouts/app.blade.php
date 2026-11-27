<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DonorDarah')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">
@if (auth()->guest())
    @php
        $isAuthPage = request()->routeIs('login', 'register');
        $guestMainClass = 'container py-4 py-lg-5';

        if ($isAuthPage) {
            $guestMainClass = 'auth-page';
        }
    @endphp

    @if (!$isAuthPage)
        <nav class="navbar navbar-expand-lg public-navbar bg-white border-bottom sticky-top">
            <div class="container">
                <a class="navbar-brand fw-bold text-danger d-flex align-items-center gap-2" href="{{ route('home') }}">
                    <img src="{{ asset('images/logo-donor.png') }}" class="site-logo" alt="Logo DonorDarah" width="36" height="36">
                    DonorDarah
                </a>

                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#publicNavbar"
                    aria-controls="publicNavbar"
                    aria-expanded="false"
                    aria-label="Buka navigasi"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="publicNavbar">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0" data-section-nav>
                        <li class="nav-item">
                            <a
                                class="nav-link active"
                                data-section-link="beranda"
                                href="{{ route('home') }}#beranda"
                            >
                                Beranda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-section-link="informasi" href="{{ route('home') }}#informasi">
                                Informasi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link"
                                data-section-link="layanan"
                                href="{{ route('home') }}#layanan"
                            >
                                Layanan
                            </a>
                            <span class="nav-indicator" aria-hidden="true"></span>
                        </li>
                    </ul>

                    <div class="d-flex gap-2 align-items-center">
                        <a class="btn btn-outline-danger px-4" href="{{ route('login') }}">
                            Masuk
                        </a>
                        <a class="btn btn-danger px-4" href="{{ route('register') }}">
                            Daftar
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    @endif

    <main class="{{ $guestMainClass }} page-transition">
@else
    @php
        $isAdmin = auth()->user()->role === 'admin';
        $appHome = route('dashboard');
        $pageTitle = 'Dashboard';
        $userRoleLabel = 'Pendonor';

        if ($isAdmin) {
            $appHome = route('admin.dashboard');
            $pageTitle = 'Dashboard Admin';
            $userRoleLabel = 'Administrator';
        }

    @endphp

    <div class="app-shell">
        <aside class="app-sidebar d-none d-lg-flex flex-column">
            <a class="sidebar-brand text-white text-decoration-none" href="{{ $appHome }}">
                <img src="{{ asset('images/logo-donor.png') }}" class="site-logo site-logo-sidebar" alt="Logo DonorDarah" width="36" height="36">
                DonorDarah
            </a>

            <div class="sidebar-label">MENU UTAMA</div>

            @if ($isAdmin)
                <a @class(['sidebar-link', 'active' => request()->routeIs('admin.dashboard')]) href="{{ route('admin.dashboard') }}">
                    <span>▦</span> Dashboard
                </a>
                <a @class(['sidebar-link', 'active' => request()->routeIs('admin.accounts.*')]) href="{{ route('admin.accounts.index') }}">
                    <span>♙</span> Verifikasi Akun
                </a>
                <a @class(['sidebar-link', 'active' => request()->routeIs('admin.schedules.*')]) href="{{ route('admin.schedules.index') }}">
                    <span>▣</span> Jadwal Donor
                </a>
                <a @class(['sidebar-link', 'active' => request()->routeIs('admin.registrations.*')]) href="{{ route('admin.registrations.index') }}">
                    <span>☷</span> Verifikasi Pendaftaran
                </a>
                <a @class(['sidebar-link', 'active' => request()->routeIs('admin.announcements.*')]) href="{{ route('admin.announcements.index') }}">
                    <span>▧</span> Pengumuman
                </a>
            @else
                <a @class(['sidebar-link', 'active' => request()->routeIs('dashboard')]) href="{{ route('dashboard') }}">
                    <span>▦</span> Dashboard
                </a>
                <a @class(['sidebar-link', 'active' => request()->routeIs('services')]) href="{{ route('services') }}">
                    <span>▣</span> Layanan Donor
                </a>
                <a @class(['sidebar-link', 'active' => request()->routeIs('donor.registrations.*')]) href="{{ route('donor.registrations.index') }}">
                    <span>◷</span> Riwayat Donor
                </a>
                <a @class(['sidebar-link', 'active' => request()->routeIs('profile.*')]) href="{{ route('profile.edit') }}">
                    <span>♙</span> Profil Saya
                </a>
            @endif

            <div class="sidebar-spacer"></div>

            <form action="{{ route('logout') }}" method="POST" class="sidebar-logout">
                @csrf
                <button type="submit" class="sidebar-link w-100 text-start border-0">
                    <span class="logout-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v10" />
                            <path d="M5.6 5.6a8.5 8.5 0 1 0 12.8 0" />
                        </svg>
                    </span>
                    Keluar
                </button>
            </form>
        </aside>

        <div class="app-main">
            <header class="app-topbar bg-white border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <button
                        class="btn btn-outline-danger d-lg-none"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#mobileSidebar"
                        aria-controls="mobileSidebar"
                        aria-label="Buka menu"
                    >
                        ☰
                    </button>

                    <div>
                        <h1 class="h4 fw-bold mb-0">@yield('page-title', $pageTitle)</h1>
                        <div class="small text-secondary d-none d-sm-block">
                            Sistem Pendaftaran Donor Darah Online
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span class="avatar-circle">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </span>
                    <div class="d-none d-sm-block">
                        <div class="small fw-semibold">{{ auth()->user()->name }}</div>
                        <div class="small text-secondary">{{ $userRoleLabel }}</div>
                    </div>
                </div>
            </header>

            <div
                class="offcanvas offcanvas-start app-mobile-sidebar"
                tabindex="-1"
                id="mobileSidebar"
                aria-labelledby="mobileSidebarLabel"
            >
                <div class="offcanvas-header app-sidebar-mobile-head">
                    <h2 class="offcanvas-title h5 fw-bold text-white" id="mobileSidebarLabel">
                        <img src="{{ asset('images/logo-donor.png') }}" class="site-logo site-logo-sidebar" alt="Logo DonorDarah" width="36" height="36">
                        DonorDarah
                    </h2>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="offcanvas"
                        aria-label="Tutup"
                    ></button>
                </div>

                <div class="offcanvas-body d-flex flex-column">
                    @if ($isAdmin)
                        <a @class(['sidebar-link', 'active' => request()->routeIs('admin.dashboard')]) href="{{ route('admin.dashboard') }}">▦ Dashboard</a>
                        <a @class(['sidebar-link', 'active' => request()->routeIs('admin.accounts.*')]) href="{{ route('admin.accounts.index') }}">♙ Verifikasi Akun</a>
                        <a @class(['sidebar-link', 'active' => request()->routeIs('admin.schedules.*')]) href="{{ route('admin.schedules.index') }}">▣ Jadwal Donor</a>
                        <a @class(['sidebar-link', 'active' => request()->routeIs('admin.registrations.*')]) href="{{ route('admin.registrations.index') }}">☷ Verifikasi Pendaftaran</a>
                        <a @class(['sidebar-link', 'active' => request()->routeIs('admin.announcements.*')]) href="{{ route('admin.announcements.index') }}">▧ Pengumuman</a>
                    @else
                        <a @class(['sidebar-link', 'active' => request()->routeIs('dashboard')]) href="{{ route('dashboard') }}">▦ Dashboard</a>
                        <a @class(['sidebar-link', 'active' => request()->routeIs('services')]) href="{{ route('services') }}">▣ Layanan Donor</a>
                        <a @class(['sidebar-link', 'active' => request()->routeIs('donor.registrations.*')]) href="{{ route('donor.registrations.index') }}">◷ Riwayat Donor</a>
                        <a @class(['sidebar-link', 'active' => request()->routeIs('profile.*')]) href="{{ route('profile.edit') }}">♙ Profil Saya</a>
                    @endif

                    <div class="sidebar-spacer"></div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="sidebar-link w-100 text-start border-0" type="submit">
                            <span class="logout-icon" aria-hidden="true">↪</span>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>

            <main class="app-content page-transition">
@endif
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @yield('content')

@if (auth()->guest())
    </main>

    @if (!$isAuthPage)
        <footer class="border-top bg-white mt-5">
            <div class="container py-4 d-flex flex-column flex-md-row justify-content-between gap-2">
                <span class="fw-semibold text-danger">🩸 DonorDarah</span>
                <span class="text-secondary small">Sistem Pendaftaran Donor Darah Online</span>
            </div>
        </footer>
    @endif
@else
            </main>
        </div>
    </div>
@endif
</body>
</html>
