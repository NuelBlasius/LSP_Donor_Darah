
@extends('layouts.app')

@section('title', 'Profil Saya | DonorDarah')
@section('page-title', 'Profil Saya')

@section('content')
<div class="container-fluid py-4">

    {{-- Header halaman --}}
    <div class="row justify-content-center">
        <div class="col-xl-9">

            <div class="mb-4">
                <h1 class="h2 fw-bold">Data Profil Pendonor</h1>
                <p class="text-secondary mb-0">
                    Perbarui data yang akan ditampilkan otomatis
                    saat mendaftar jadwal donor.
                </p>
            </div>

            {{-- Pesan sukses --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert"
                            aria-label="Close">
                    </button>
                </div>
            @endif

            {{-- Pesan error --}}
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <strong>Periksa kembali data yang Anda masukkan.</strong>

                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form profil --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="row g-3">

                            {{-- Nama lengkap --}}
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    Nama Lengkap
                                </label>

                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}"
                                    autocomplete="name"
                                    required
                                >

                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Nomor telepon --}}
                            <div class="col-md-6">
                                <label for="nomor_telepon" class="form-label">
                                    Nomor Telepon
                                </label>

                                <input
                                    type="tel"
                                    id="nomor_telepon"
                                    name="nomor_telepon"
                                    class="form-control @error('nomor_telepon') is-invalid @enderror"
                                    value="{{ old('nomor_telepon', $user->nomor_telepon) }}"
                                    autocomplete="tel"
                                    required
                                >

                                @error('nomor_telepon')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- NIK --}}
                            <div class="col-md-6">
                                <label for="nik" class="form-label">
                                    NIK (16 digit)
                                </label>

                                <input
                                    type="text"
                                    id="nik"
                                    name="nik"
                                    class="form-control @error('nik') is-invalid @enderror"
                                    inputmode="numeric"
                                    pattern="[0-9]{16}"
                                    maxlength="16"
                                    minlength="16"
                                    value="{{ old('nik', $user->nik) }}"
                                    required
                                >

                                @error('nik')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    Email
                                </label>

                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}"
                                    autocomplete="email"
                                    required
                                >

                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Jenis kelamin --}}
                            <div class="col-md-4">
                                <label for="jenis_kelamin" class="form-label">
                                    Jenis Kelamin
                                </label>

                                <select
                                    id="jenis_kelamin"
                                    name="jenis_kelamin"
                                    class="form-select @error('jenis_kelamin') is-invalid @enderror"
                                    required
                                >
                                    <option value="">Pilih jenis kelamin</option>

                                    <option
                                        value="Laki-laki"
                                        @selected(old('jenis_kelamin', $user->jenis_kelamin) === 'Laki-laki')
                                    >
                                        Laki-laki
                                    </option>

                                    <option
                                        value="Perempuan"
                                        @selected(old('jenis_kelamin', $user->jenis_kelamin) === 'Perempuan')
                                    >
                                        Perempuan
                                    </option>
                                </select>

                                @error('jenis_kelamin')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Golongan darah --}}
                            <div class="col-md-4">
                                <label for="golongan_darah" class="form-label">
                                    Golongan Darah
                                </label>

                                <select
                                    id="golongan_darah"
                                    name="golongan_darah"
                                    class="form-select @error('golongan_darah') is-invalid @enderror"
                                    required
                                >
                                    <option value="">Pilih golongan darah</option>

                                    @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $blood)
                                        <option
                                            value="{{ $blood }}"
                                            @selected(old('golongan_darah', $user->golongan_darah) === $blood)
                                        >
                                            {{ $blood }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('golongan_darah')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Tanggal lahir --}}
                            <div class="col-md-4">
                                <label for="tanggal_lahir" class="form-label">
                                    Tanggal Lahir
                                </label>

                                <input
                                    type="date"
                                    id="tanggal_lahir"
                                    name="tanggal_lahir"
                                    class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                    value="{{ old('tanggal_lahir', $user->tanggal_lahir?->format('Y-m-d')) }}"
                                    max="{{ now()->toDateString() }}"
                                    required
                                >

                                @error('tanggal_lahir')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Alamat --}}
                            <div class="col-12">
                                <label for="alamat" class="form-label">
                                    Alamat Lengkap
                                </label>

                                <textarea
                                    id="alamat"
                                    name="alamat"
                                    rows="3"
                                    class="form-control @error('alamat') is-invalid @enderror"
                                    autocomplete="street-address"
                                    required
                                >{{ old('alamat', $user->alamat) }}</textarea>

                                @error('alamat')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                        </div>

                        {{-- Tombol --}}
                        <div class="d-flex flex-wrap gap-2 mt-4">

                            <button
                                class="btn btn-danger px-4"
                                type="submit"
                            >
                                Simpan Profil
                            </button>

                            <a
                                href="{{ route('dashboard') }}"
                                class="btn btn-outline-secondary px-4"
                            >
                                Kembali ke Dashboard
                            </a>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>
@endsection
