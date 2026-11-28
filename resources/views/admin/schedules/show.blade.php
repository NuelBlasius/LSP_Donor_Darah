@extends('layouts.app')

@section('title', $schedule->title . ' | Jadwal Donor')
@section('page-title', 'Detail Jadwal Donor')

@section('content')
@php
    $scheduleIsFull = $schedule->registered_count >= $schedule->quota;
    $scheduleStatusText = $scheduleIsFull ? 'Penuh' : ($schedule->status === 'open' ? 'Dibuka' : 'Ditutup');
    $scheduleBadgeClass = $scheduleIsFull ? 'text-bg-danger' : ($schedule->status === 'open' ? 'text-bg-success' : 'text-bg-secondary');
@endphp
<div class="d-flex flex-column flex-xl-row justify-content-between align-items-start gap-3 mb-4">
    <div>
        <a href="{{ route('admin.schedules.index') }}" class="small text-danger text-decoration-none">← Kembali ke daftar jadwal</a>
        <h1 class="h2 fw-bold mt-2 mb-1">{{ $schedule->title }}</h1>
        <p class="text-secondary mb-0">Informasi kegiatan dan daftar akun yang mendaftar pada jadwal ini.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.schedules.edit', $schedule) }}" class="btn btn-outline-primary">Edit jadwal</a>
        <form method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}" onsubmit="return confirm('Hapus jadwal ini? Pendaftaran terkait juga akan terhapus.');">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline-danger" type="submit">Hapus jadwal</button>
        </form>
    </div>
</div>

<section class="card border-0 shadow-sm mb-4" aria-labelledby="scheduleInfoTitle">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
            <div>
                <div class="small text-secondary">Informasi jadwal</div>
                <h2 class="h5 fw-bold mb-0" id="scheduleInfoTitle">Detail kegiatan donor</h2>
            </div>
            <span class="badge {{ $scheduleBadgeClass }}">{{ $scheduleStatusText }}</span>
        </div>
        <div class="row g-3">
            <div class="col-md-6 col-xl-4"><div class="detail-field"><div class="detail-label">Tanggal dan waktu</div><div class="fw-semibold">{{ $schedule->schedule_date->format('d M Y') }} · {{ substr($schedule->start_time, 0, 5) }}–{{ substr($schedule->end_time, 0, 5) }}</div></div></div>
            <div class="col-md-6 col-xl-4"><div class="detail-field"><div class="detail-label">Lokasi</div><div>@if ($schedule->map_url)<a class="map-location" href="{{ $schedule->map_url }}" target="_blank" rel="noopener noreferrer">{{ $schedule->map_address }}</a>@else{{ $schedule->map_address }}@endif</div></div></div>
            <div class="col-md-6 col-xl-4"><div class="detail-field"><div class="detail-label">Kuota pendonor</div><div class="fw-semibold">{{ $schedule->registered_count }}/{{ $schedule->quota }} pendaftar</div></div></div>
            <div class="col-md-6 col-xl-4"><div class="detail-field"><div class="detail-label">Nominal pembayaran</div><div class="fw-bold text-danger">Rp {{ number_format((int) $schedule->amount, 0, ',', '.') }}</div></div></div>
        </div>
    </div>
</section>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-2 mb-3">
    <div><h2 class="h4 fw-bold mb-1">Pendaftar</h2><p class="text-secondary mb-0">Pilih baris untuk memeriksa data pendaftaran dan bukti pembayaran.</p></div>
    <span class="badge rounded-pill text-bg-light border text-dark">{{ $registrations->total() }} akun</span>
</div>

<form method="GET" action="{{ route('admin.schedules.show', $schedule) }}" class="card border-0 shadow-sm p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-6"><label for="search" class="form-label">Nama pendonor</label><input id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari berdasarkan nama"></div>
        <div class="col-md-4"><label for="date" class="form-label">Tanggal mendaftar</label><input id="date" type="date" name="date" class="form-control" value="{{ request('date') }}"></div>
        <div class="col-md-2 d-flex gap-2"><button class="btn btn-danger flex-grow-1" type="submit">Cari</button><a class="btn btn-outline-secondary" href="{{ route('admin.schedules.show', $schedule) }}">Reset</a></div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Pendonor</th><th>Email</th><th>Tanggal mendaftar</th><th>Status pendaftaran</th><th>Status pembayaran</th></tr></thead>
            <tbody>
                @forelse ($registrations as $registration)
                    @php
                        $registrationStatusText = match ($registration->registration_status) { 'approved' => 'Disetujui', 'rejected' => 'Ditolak', default => 'Menunggu' };
                        $registrationBadgeClass = match ($registration->registration_status) { 'approved' => 'text-bg-success', 'rejected' => 'text-bg-danger', default => 'text-bg-warning' };
                        $paymentStatusText = match ($registration->payment_status) { 'approved' => 'Terverifikasi', 'rejected' => 'Ditolak', default => $registration->payment_proof ? 'Menunggu verifikasi' : 'Belum dibayar' };
                        $paymentBadgeClass = match ($registration->payment_status) { 'approved' => 'text-bg-success', 'rejected' => 'text-bg-danger', default => $registration->payment_proof ? 'text-bg-warning' : 'text-bg-secondary' };
                    @endphp
                    <tr class="table-row-link" data-row-href="{{ route('admin.schedules.registrations.show', [$schedule, $registration]) }}" tabindex="0" role="link" aria-label="Lihat pendaftaran {{ $registration->user?->name ?? 'pendonor' }}">
                        <td class="fw-semibold">{{ $registration->user?->name ?? 'Akun tidak tersedia' }}<div class="small text-danger fw-semibold">Lihat detail</div></td>
                        <td>{{ $registration->user?->email ?? '-' }}</td>
                        <td>{{ $registration->created_at?->format('d M Y') ?? '-' }}</td>
                        <td><span class="badge {{ $registrationBadgeClass }}">{{ $registrationStatusText }}</span></td>
                        <td><span class="badge {{ $paymentBadgeClass }}">{{ $paymentStatusText }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-secondary py-4">{{ request()->filled('search') || request()->filled('date') ? 'Tidak ada pendaftar yang cocok dengan pencarian.' : 'Belum ada akun yang mendaftar pada jadwal ini.' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($registrations->hasPages())
        <div class="card-footer bg-white">{{ $registrations->links() }}</div>
    @endif
</div>
@endsection
