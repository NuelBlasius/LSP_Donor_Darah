@extends('layouts.auth')

@section('title', 'Daftar - DonorDarah')

@section('content')
<div class="auth-shell">
    <section class="auth-art">
        <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 mb-4">
            <span class="brand-mark">+</span>
            <span class="brand-name">DonorDarah</span>
        </a>
        <img src="{{ asset('images/auth-blood.svg') }}" alt="" aria-hidden="true">
        <h2>Bergabung Jadi Pendonor</h2>
        <p>Daftarkan diri Anda untuk mulai berbagi harapan dan membantu sesama.</p>
    </section>

    <section class="auth-form-side">
        <div class="auth-form">
            <div class="mb-4">
                <h1>Buat Akun Baru</h1>
                <p class="text-muted mb-0">Isi data berikut untuk membuat akun pendonor.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" autocomplete="name" required>
                </div>
                <div class="mb-3">
                    <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                    <input type="tel" name="nomor_telepon" id="nomor_telepon" class="form-control" value="{{ old('nomor_telepon') }}" placeholder="Contoh: 081234567890" autocomplete="tel" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Kata Sandi</label>
                    <div class="password-wrap">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 8 karakter" autocomplete="new-password" required>
                        <button type="button" class="password-toggle" data-toggle-password="#password" aria-label="Tampilkan kata sandi">◉</button>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                    <div class="password-wrap">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi kata sandi" autocomplete="new-password" required>
                        <button type="button" class="password-toggle" data-toggle-password="#password_confirmation" aria-label="Tampilkan kata sandi">◉</button>
                    </div>
                </div>
                <button type="submit" class="btn btn-blood auth-submit w-100">Daftar</button>
            </form>

            <p class="auth-footer text-center mt-4 mb-0">
                Sudah punya akun? <a href="{{ route('login') }}" class="auth-link ms-1">Masuk di sini</a>
            </p>
            <div class="text-center mt-3"><a href="{{ route('home') }}" class="small text-muted">← Kembali ke beranda</a></div>
        </div>
    </section>
</div>
@endsection
