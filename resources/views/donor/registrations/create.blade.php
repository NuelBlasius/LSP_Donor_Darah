@extends('layouts.app')

@section('title', 'Form Pendaftaran Donor | DonorDarah')
@section('page-title', 'Form Pendaftaran Donor')

@section('content')
<div class="container-fluid px-0 donor-registration-form">
    <div class="mb-4">
        <span class="badge text-bg-danger mb-2">Pendaftaran Donor</span>
        <h1 class="h2 fw-bold mb-1">Konfirmasi Data Pendonor</h1>
        <p class="text-secondary mb-0">Data pribadi diambil dari registrasi akun Anda. Pastikan data sudah benar sebelum mengirim pendaftaran.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><h2 class="h5 fw-bold mb-0">Data Diri Pendonor</h2></div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Nama Lengkap</label><input class="form-control" value="{{ $user->name }}" readonly></div>
                        <div class="col-md-6"><label class="form-label">NIK</label><input class="form-control" value="{{ $user->nik }}" readonly></div>
                        <div class="col-md-6"><label class="form-label">Nomor Telepon</label><input class="form-control" value="{{ $user->nomor_telepon }}" readonly></div>
                        <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" value="{{ $user->email }}" readonly></div>
                        <div class="col-md-4"><label class="form-label">Jenis Kelamin</label><input class="form-control" value="{{ $user->jenis_kelamin }}" readonly></div>
                        <div class="col-md-4"><label class="form-label">Golongan Darah</label><input class="form-control" value="{{ $user->golongan_darah }}" readonly></div>
                        <div class="col-md-4"><label class="form-label">Tanggal Lahir</label><input class="form-control" value="{{ $user->tanggal_lahir->format('d-m-Y') }}" readonly></div>
                        <div class="col-md-4"><label class="form-label">Usia</label><input class="form-control" value="{{ $user->tanggal_lahir->age }} tahun" readonly></div>
                        <div class="col-md-8"><label class="form-label">Alamat</label><textarea class="form-control" rows="2" readonly>{{ $user->alamat }}</textarea></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3"><h2 class="h5 fw-bold mb-0">Jadwal yang Dipilih</h2></div>
                <div class="card-body p-4 d-flex flex-column">
                    <span class="badge text-bg-success align-self-start mb-3">{{ $scheduleStatusText }}</span>
                    <h3 class="h5 fw-bold">{{ $schedule->title }}</h3>
                    <p class="text-secondary mb-2">
                        <span aria-hidden="true">📍</span>
                        @if ($schedule->map_url)
                            <a class="map-location" href="{{ $schedule->map_url }}" target="_blank" rel="noopener noreferrer">{{ $schedule->map_address }}</a>
                        @else
                            {{ $schedule->map_address }}
                        @endif
                    </p>
                    <p class="text-secondary mb-2">📅 {{ $schedule->schedule_date->format('d M Y') }}</p>
                    <p class="text-secondary mb-4">🕒 {{ substr($schedule->start_time, 0, 5) }}–{{ substr($schedule->end_time, 0, 5) }}</p>
                    <div class="mt-auto">
                        <form method="POST" action="{{ route('donor.registrations.store') }}">
                            @csrf
                            <input type="hidden" name="donor_schedule_id" value="{{ $schedule->id }}">
                            <div class="form-check mb-3"><input class="form-check-input" type="checkbox" id="confirm_profile" required><label class="form-check-label small" for="confirm_profile">Saya menyatakan data diri di atas benar.</label></div>
                            <button type="submit" class="btn btn-danger btn-lg w-100">Kirim Pendaftaran</button>
                        </form>
                        <a href="{{ route('services') }}" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
