<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DonorDarah')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @if (!request()->routeIs('dashboard'))
    <nav class="navbar navbar-expand-lg site-nav">
        <div class="container py-1">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <span class="brand-mark">+</span>
                <span class="brand-name">DonorDarah</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Buka navigasi">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#informasi">Informasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#jadwal">Jadwal</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#pendaftaran">Pendaftaran</a></li>
                </ul>
                <div class="d-flex gap-2 align-items-center py-2 py-lg-0">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-blood px-4">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-blood px-4">Daftar</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-blood px-3">Dashboard</a>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-blood px-3">Keluar</button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </nav>
    @endif

    @if (session('success'))
        <div class="container pt-3">
            <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        </div>
    @endif

    @yield('content')

    @stack('scripts')
</body>
</html>
