@extends('layouts.auth')

@section('title', 'Masuk - DonorDarah')

@section('content')
<div class="auth-shell">
    <section class="auth-art">
        <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 mb-4">
            <span class="brand-mark">+</span>
            <span class="brand-name">DonorDarah</span>
        </a>
        <img src="{{ asset('images/auth-blood.svg') }}" alt="" aria-hidden="true">
        <h2>DonorDarah</h2>
        <p>Sistem pendaftaran donor darah secara online. Bersama, kita bantu lebih banyak kehidupan.</p>
    </section>

    <section class="auth-form-side">
        <div class="auth-form">
            <div class="mb-4">
                <h1>Masuk ke Akun Anda</h1>
                <p class="text-muted mb-0">Silakan login untuk mengakses layanan donor darah.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="email" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <div class="password-wrap">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
                        <button type="button" class="password-toggle" data-toggle-password="#password" aria-label="Tampilkan kata sandi">◉</button>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <label class="form-check d-flex align-items-center gap-2 mb-0">
                        <input type="checkbox" name="remember" class="form-check-input mt-0">
                        <span class="small">Ingat saya</span>
                    </label>
                    <span class="small text-muted">Lupa kata sandi? Hubungi admin.</span>
                </div>
                <button type="submit" class="btn btn-blood auth-submit w-100">Masuk</button>
            </form>

            <p class="auth-footer text-center mt-4 mb-0">
                Belum punya akun? <a href="{{ route('register') }}" class="auth-link ms-1">Daftar di sini</a>
            </p>
            <div class="text-center mt-4"><a href="{{ route('home') }}" class="small text-muted">← Kembali ke beranda</a></div>
        </div>
    </section>
</div>
@endsection
