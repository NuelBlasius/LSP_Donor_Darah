@extends('layouts.app')

@section('title', 'Layanan | DonorDarah')

@section('content')
<div class="mb-4">
    <span class="badge text-bg-danger mb-2">Layanan Donor</span>
    <h1 class="h2 fw-bold">Layanan & Jadwal Donor</h1>
    <p class="text-secondary">Pilih jadwal yang tersedia. Pendaftaran hanya dapat dilakukan oleh akun pendonor yang telah disetujui.</p>
</div>

<div class="row g-3 mb-5">
    @forelse ($schedules as $schedule)
        @php
            $registered = auth()->check() && auth()->user()->donorRegistrations()
                ->where('donor_schedule_id', $schedule->id)
                ->whereIn('registration_status', ['pending', 'approved'])
                ->exists();
            $taken = $schedule->registrations()->whereIn('registration_status', ['pending', 'approved'])->count();
            $remaining = max(0, $schedule->quota - $taken);
        @endphp
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <span class="badge text-bg-success">Dibuka</span>
                        <span class="small text-secondary">Sisa kuota: {{ $remaining }}</span>
                    </div>
                    <h2 class="h5 fw-bold mt-3">{{ $schedule->title }}</h2>
                    <p class="text-secondary mb-2">📍 {{ $schedule->location }}</p>
                    <p class="text-secondary mb-2">📅 {{ $schedule->schedule_date->format('d M Y') }}</p>
                    <p class="text-secondary mb-3">🕒 {{ substr($schedule->start_time, 0, 5) }}–{{ substr($schedule->end_time, 0, 5) }}</p>

                    @if ($remaining < 1)
                        <button class="btn btn-secondary w-100" disabled>Kuota Penuh</button>
                    @elseif (!auth()->check())
                        <a href="{{ route('login') }}" class="btn btn-danger w-100">Masuk untuk Mendaftar</a>
                    @elseif (auth()->user()->account_status !== 'approved')
                        <button class="btn btn-outline-secondary w-100" disabled>Menunggu Verifikasi Akun</button>
                    @elseif ($registered)
                        <a href="{{ route('donor.registrations.index') }}" class="btn btn-outline-success w-100">Sudah Terdaftar · Lihat Status</a>
                    @else
                        <a href="{{ route('donor.registrations.create', $schedule) }}" class="btn btn-danger w-100">Daftar Jadwal Ini</a>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="alert alert-info mb-0">Belum ada jadwal donor yang dibuka saat ini.</div></div>
    @endforelse
</div>

@endsection
