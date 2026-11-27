@extends('layouts.app')

@section('title', 'Daftar | DonorDarah')

@section('content')
<div class="auth-shell auth-shell-register">
    <div class="row g-0 h-100">
        <div class="col-lg-5 auth-brand-panel">
            <div class="auth-brand-content">
                <img src="{{ asset('images/register-donor.png') }}" class="auth-illustration content-illustration" alt="Ilustrasi registrasi akun donor darah">
                <h2 class="display-6 fw-bold text-white text-center mt-3">DonorDarah</h2>
                <p class="text-white text-center opacity-75 mb-0">Bergabung dan jadilah pendonor darah untuk membantu sesama.</p>
            </div>
        </div>
        <div class="col-lg-7 bg-white auth-form-panel">
            <div class="auth-form-inner">
                <h1 class="h2 fw-bold">Buat Akun Baru</h1>
                <p class="text-secondary mb-4">Daftarkan diri Anda untuk mulai mengakses layanan donor darah.</p>
                @if ($errors->any())
                    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ route('register.store') }}">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" autocomplete="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                            <input id="nomor_telepon" type="tel" name="nomor_telepon" class="form-control" value="{{ old('nomor_telepon') }}" placeholder="08xxxxxxxxxx" autocomplete="tel" required>
                        </div>
                        <div class="col-md-6">
                            <label for="nik" class="form-label">NIK (16 digit)</label>
                            <input id="nik" type="text" inputmode="numeric" pattern="[0-9]{16}" maxlength="16" name="nik" class="form-control" value="{{ old('nik') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                            <select id="jenis_kelamin" name="jenis_kelamin" class="form-select" required>
                                <option value="">Pilih jenis kelamin</option>
                                <option value="Laki-laki" @selected(old('jenis_kelamin') === 'Laki-laki')>Laki-laki</option>
                                <option value="Perempuan" @selected(old('jenis_kelamin') === 'Perempuan')>Perempuan</option>
                                 layanan dono
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="golongan_darah" class="form-label">Golongan Darah</label>
                            <select id="golongan_darah" name="golongan_darah" class="form-select" required>
                                <option value="">Pilih golongan darah</option>
                                @foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $blood)
                                    <option value="{{ $blood }}" @selected(old('golongan_darah') === $blood)>{{ $blood }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input id="tanggal_lahir" type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" max="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="col-12">
                            <label for="alamat" class="form-label">Alamat Lengkap</label>
                            <textarea id="alamat" name="alamat" rows="2" class="form-control" autocomplete="street-address" required>{{ old('alamat') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" autocomplete="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Kata Sandi</label>
                            <label for="password" class="form-label">(minimal 8 karakter)</label>
                            <input id="password" type="password" name="password" class="form-control" autocomplete="new-password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" autocomplete="new-password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-danger btn-lg w-100 mt-4">Daftar</button>
                </form>
                <p class="text-center mt-4 mb-0">Sudah punya akun? <a class="link-danger fw-semibold" href="{{ route('login') }}">Masuk di sini</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
