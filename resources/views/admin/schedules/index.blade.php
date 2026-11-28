@extends('layouts.app')

@section('title', 'Jadwal Donor | DonorDarah')
@section('page-title', 'Jadwal Donor')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h1 class="h2 fw-bold mb-1">{{ request('queue') === 'registrations' ? 'Pendaftaran Menunggu' : (request('queue') === 'payments' ? 'Pembayaran Menunggu' : 'Jadwal Donor') }}</h1>
        <p class="text-secondary mb-0">{{ request('queue') ? 'Jadwal yang memiliki ' . (request('queue') === 'registrations' ? 'pendaftaran' : 'bukti pembayaran') . ' menunggu verifikasi. Pilih jadwal untuk memeriksanya.' : 'Pilih jadwal untuk melihat pendaftar, memeriksa pembayaran, dan mengelola kegiatan donor.' }}</p>
    </div>
    <a href="{{ route('admin.schedules.create') }}" class="btn btn-danger btn-lg d-inline-flex align-items-center justify-content-center flex-shrink-0 px-4">Tambah Jadwal</a>
</div>

<form method="GET" action="{{ route('admin.schedules.index') }}" class="card border-0 shadow-sm p-3 mb-3">
    @if (request('queue'))
        <input type="hidden" name="queue" value="{{ request('queue') }}">
    @endif
    <div class="row g-2 align-items-end">
        <div class="col-md-6">
            <label for="search" class="form-label">Nama jadwal</label>
            <input id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari nama jadwal donor">
        </div>
        <div class="col-md-4">
            <label for="date" class="form-label">Tanggal donor</label>
            <input id="date" type="date" name="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-danger flex-grow-1" type="submit">Cari</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.schedules.index', request('queue') ? ['queue' => request('queue')] : []) }}">Reset</a>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Nama Jadwal</th><th>Tanggal</th><th>Lokasi</th><th>Kuota</th><th>Nominal</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse ($schedules as $schedule)
                    @php
                        $scheduleIsFull = $schedule->registered_count >= $schedule->quota;
                        $scheduleBadgeClass = $scheduleIsFull
                            ? 'text-bg-danger'
                            : ($schedule->status === 'open' ? 'text-bg-success' : 'text-bg-secondary');
                        $scheduleStatusText = $scheduleIsFull
                            ? 'Penuh'
                            : ($schedule->status === 'open' ? 'Dibuka' : 'Ditutup');
                    @endphp
                    <tr class="table-row-link" data-row-href="{{ route('admin.schedules.show', $schedule) }}" tabindex="0" role="link" aria-label="Lihat detail jadwal {{ $schedule->title }}">
                        <td class="fw-semibold">{{ $schedule->title }}<div class="small text-danger fw-semibold">Lihat detail jadwal</div></td>
                        <td>{{ $schedule->schedule_date->format('d M Y') }}<div class="small text-secondary">{{ substr($schedule->start_time, 0, 5) }}–{{ substr($schedule->end_time, 0, 5) }}</div></td>
                        <td>
                            @if ($schedule->map_url)
                                <a class="map-location" href="{{ $schedule->map_url }}" target="_blank" rel="noopener noreferrer">{{ $schedule->map_address }}</a>
                            @else
                                {{ $schedule->map_address }}
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $schedule->registered_count }}/{{ $schedule->quota }}</td>
                        <td>Rp {{ number_format((int) $schedule->amount, 0, ',', '.') }}</td>
                        <td><span class="badge {{ $scheduleBadgeClass }}">{{ $scheduleStatusText }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-secondary py-4">Belum ada jadwal donor. Klik Tambah Jadwal untuk membuat jadwal.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($schedules->hasPages())
        <div class="card-footer bg-white">{{ $schedules->links() }}</div>
    @endif
</div>
@endsection
