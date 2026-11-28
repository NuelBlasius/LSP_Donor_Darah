@extends('layouts.app')

@section('title', 'Masuk | DonorDarah')

@section('content')
<div class="auth-shell">
    <div class="row g-0 h-100">
        <div class="col-lg-5 auth-brand-panel">
            <div class="auth-brand-content">
                <img src="{{ asset('images/Gambar_Login_Page.webp') }}" class="auth-illustration content-illustration" alt="Ilustrasi halaman login donor darah">
                <h2 class="display-6 fw-bold text-white text-center mt-3">DonorDarah</h2>
                <p class="text-white text-center opacity-75 mb-0">Sistem Pendaftaran Donor Darah Secara Online</p>
            </div>
        </div>
        <div class="col-lg-7 bg-white auth-form-panel">
            <div class="auth-form-inner">
                <h1 class="h2 fw-bold">Masuk ke Akun Anda</h1>
                <p class="text-secondary mb-4">Silakan login untuk mengakses layanan pendaftaran donor darah.</p>
                @if ($errors->any())
                    <div class="alert alert-danger">@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
                @endif
                <form method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group input-group-lg"><span class="input-group-text bg-white">✉</span><input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="email" required autofocus></div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Kata Sandi</label>
                        <div class="input-group input-group-lg"><span class="input-group-text bg-white">▣</span><input id="password" type="password" name="password" class="form-control" placeholder="Masukkan kata sandi" autocomplete="current-password" required></div>
                    </div>
                    <div class="form-check mb-4"><input class="form-check-input" type="checkbox" name="remember" id="remember"><label class="form-check-label" for="remember">Ingat saya</label></div>
                    <button type="submit" class="btn btn-danger btn-lg w-100">Masuk</button>
                </form>
                <p class="text-center mt-4 mb-0">Belum punya akun? <a class="link-danger fw-semibold" href="{{ route('register') }}">Daftar di sini</a></p>
                <p class="text-center mt-3"><a href="{{ route('home') }}" class="link-secondary small text-decoration-none">← Kembali ke Beranda</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
