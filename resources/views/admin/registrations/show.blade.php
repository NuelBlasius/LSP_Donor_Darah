@extends('layouts.app')

@section('title', 'Detail Pendaftaran | ' . ($registration->user?->name ?? 'Pendonor'))
@section('page-title', 'Detail Pendaftaran')

@section('content')
@php
    $person = $registration->user;
    $nik = $person?->nik ?? '-';
    $phone = $person?->nomor_telepon ?? '-';
    $maskedNik = $nik !== '-' ? str_repeat('•', max(0, strlen($nik) - 4)) . substr($nik, -4) : '-';
    $maskedPhone = $phone !== '-' ? str_repeat('•', max(0, strlen($phone) - 3)) . substr($phone, -3) : '-';
    $registrationStatusText = match ($registration->registration_status) { 'approved' => 'Disetujui', 'rejected' => 'Ditolak', default => 'Menunggu verifikasi' };
    $registrationBadgeClass = match ($registration->registration_status) { 'approved' => 'text-bg-success', 'rejected' => 'text-bg-danger', default => 'text-bg-warning' };
    $paymentStatusText = match ($registration->payment_status) { 'approved' => 'Terverifikasi', 'rejected' => 'Ditolak', default => $registration->payment_proof ? 'Menunggu verifikasi' : 'Belum dibayar' };
    $paymentBadgeClass = match ($registration->payment_status) { 'approved' => 'text-bg-success', 'rejected' => 'text-bg-danger', default => $registration->payment_proof ? 'text-bg-warning' : 'text-bg-secondary' };
@endphp

<div class="mb-4">
    <a href="{{ route('admin.schedules.show', $schedule) }}" class="small text-danger text-decoration-none">← Kembali ke pendaftar jadwal</a>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mt-2">
        <div><h1 class="h2 fw-bold mb-1">{{ $person?->name ?? 'Akun pendonor tidak tersedia' }}</h1><p class="text-secondary mb-0">{{ $schedule->title }} · {{ $schedule->schedule_date->format('d M Y') }}</p></div>
        <div class="d-flex flex-wrap gap-2"><span class="badge {{ $registrationBadgeClass }}">Pendaftaran: {{ $registrationStatusText }}</span><span class="badge {{ $paymentBadgeClass }}">Pembayaran: {{ $paymentStatusText }}</span></div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-5">
        <section class="card border-0 shadow-sm h-100" aria-labelledby="donorInfoTitle">
            <div class="card-body p-4">
                <div class="small text-danger fw-semibold mb-1">DATA PENDAFTAR</div>
                <h2 class="h5 fw-bold mb-3" id="donorInfoTitle">Informasi pendonor</h2>
                <div class="detail-field"><div class="detail-label">Nama lengkap</div><div class="fw-semibold">{{ $person?->name ?? '-' }}</div></div>
                <div class="detail-field"><div class="detail-label">Email</div><div>{{ $person?->email ?? '-' }}</div></div>
                <div class="detail-field"><div class="detail-label">NIK</div><div class="d-flex align-items-center gap-2"><span class="sensitive-value" data-full="{{ $nik }}" data-masked="{{ $maskedNik }}">{{ $maskedNik }}</span>@if ($nik !== '-')<button type="button" class="btn btn-sm btn-outline-secondary reveal-sensitive" aria-label="Tampilkan NIK" data-revealed="false">Lihat</button>@endif</div></div>
                <div class="detail-field"><div class="detail-label">Nomor telepon</div><div class="d-flex align-items-center gap-2"><span class="sensitive-value" data-full="{{ $phone }}" data-masked="{{ $maskedPhone }}">{{ $maskedPhone }}</span>@if ($phone !== '-')<button type="button" class="btn btn-sm btn-outline-secondary reveal-sensitive" aria-label="Tampilkan nomor telepon" data-revealed="false">Lihat</button>@endif</div></div>
                <div class="row g-3">
                    <div class="col-sm-6"><div class="detail-field"><div class="detail-label">Jenis kelamin</div><div>{{ $person?->jenis_kelamin ?? '-' }}</div></div></div>
                    <div class="col-sm-6"><div class="detail-field"><div class="detail-label">Golongan darah</div><div>{{ $person?->golongan_darah ?? '-' }}</div></div></div>
                    <div class="col-sm-6"><div class="detail-field"><div class="detail-label">Tanggal lahir</div><div>{{ $person?->tanggal_lahir?->format('d M Y') ?? '-' }}</div></div></div>
                    <div class="col-sm-6"><div class="detail-field"><div class="detail-label">Tanggal mendaftar</div><div>{{ $registration->created_at?->format('d M Y, H:i') ?? '-' }}</div></div></div>
                </div>
                <div class="detail-field"><div class="detail-label">Alamat</div><div>{{ $person?->alamat ?? '-' }}</div></div>
            </div>
        </section>
    </div>

    <div class="col-xl-7">
        <section class="card border-0 shadow-sm mb-4" aria-labelledby="scheduleInfoTitle">
            <div class="card-body p-4">
                <div class="small text-danger fw-semibold mb-1">JADWAL DONOR</div>
                <h2 class="h5 fw-bold mb-3" id="scheduleInfoTitle">{{ $schedule->title }}</h2>
                <div class="row g-3">
                    <div class="col-md-6"><div class="detail-field"><div class="detail-label">Tanggal dan waktu</div><div>{{ $schedule->schedule_date->format('d M Y') }} · {{ substr($schedule->start_time, 0, 5) }}–{{ substr($schedule->end_time, 0, 5) }}</div></div></div>
                    <div class="col-md-6"><div class="detail-field"><div class="detail-label">Nominal pembayaran</div><div class="fw-bold text-danger">Rp {{ number_format((int) $schedule->amount, 0, ',', '.') }}</div></div></div>
                    <div class="col-12"><div class="detail-field"><div class="detail-label">Lokasi</div><div>@if ($schedule->map_url)<a class="map-location" href="{{ $schedule->map_url }}" target="_blank" rel="noopener noreferrer">{{ $schedule->map_address }}</a>@else{{ $schedule->map_address }}@endif</div></div></div>
                </div>
            </div>
        </section>

        <section class="card border-0 shadow-sm" aria-labelledby="paymentInfoTitle">
            <div class="card-body p-4">
                <div class="small text-danger fw-semibold mb-1">VERIFIKASI</div>
                <h2 class="h5 fw-bold mb-3" id="paymentInfoTitle">Status pendaftaran dan pembayaran</h2>
                <div class="row g-3 mb-3">
                    <div class="col-md-6"><div class="detail-field"><div class="detail-label">Status pendaftaran</div><span class="badge {{ $registrationBadgeClass }}">{{ $registrationStatusText }}</span></div></div>
                    <div class="col-md-6"><div class="detail-field"><div class="detail-label">Metode pembayaran</div><div>{{ $registration->payment_method === 'virtual_account' ? 'Virtual Account' : ($registration->payment_method === 'qris' ? 'QRIS' : 'Belum dipilih') }}</div></div></div>
                </div>

                <div class="detail-label mb-2">Bukti pembayaran</div>
                @if ($registration->payment_proof)
                    <button type="button" class="proof-preview-trigger border-0 p-0 bg-transparent" data-proof-src="{{ asset('storage/' . $registration->payment_proof) }}" aria-label="Buka preview bukti pembayaran ukuran penuh">
                        <img src="{{ asset('storage/' . $registration->payment_proof) }}" class="img-fluid proof-preview rounded-3" alt="Bukti pembayaran dari {{ $person?->name ?? 'pendonor' }}">
                    </button>
                    <p class="small text-secondary mt-2 mb-0">Pilih gambar untuk melihat ukuran penuhnya.</p>
                @else
                    <div class="border rounded-3 p-4 text-secondary text-center bg-light">Pendonor belum mengunggah bukti pembayaran.</div>
                @endif

                <div class="d-flex flex-column flex-sm-row flex-wrap gap-2 border-top pt-3 mt-4">
                    @if ($registration->registration_status === 'pending')
                        <form method="POST" action="{{ route('admin.registrations.status', $registration) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="registration_status" value="approved">
                            <button class="btn btn-success" type="submit">Verifikasi pendaftaran</button>
                        </form>
                        <form method="POST" action="{{ route('admin.registrations.status', $registration) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="registration_status" value="rejected">
                            <button class="btn btn-outline-danger" type="submit">Tolak pendaftaran</button>
                        </form>
                    @endif
                    @if ($registration->payment_proof && $registration->payment_status === 'pending')
                        <form method="POST" action="{{ route('admin.payments.status', $registration) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="payment_status" value="approved">
                            <button class="btn btn-primary" type="submit">Verifikasi pembayaran</button>
                        </form>
                        <form method="POST" action="{{ route('admin.payments.status', $registration) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="payment_status" value="rejected">
                            <button class="btn btn-outline-danger" type="submit">Tolak bukti pembayaran</button>
                        </form>
                    @endif
                    @if ($registration->registration_status !== 'pending' && (! $registration->payment_proof || $registration->payment_status !== 'pending'))
                        <span class="small text-secondary align-self-center">Tidak ada verifikasi yang menunggu tindakan.</span>
                    @endif
                </div>
            </div>
        </section>
    </div>
</div>

<div class="proof-lightbox" id="proofLightbox" role="dialog" aria-modal="true" aria-label="Preview bukti pembayaran" hidden>
    <button type="button" class="btn btn-light proof-lightbox-close" data-proof-close aria-label="Tutup preview">Tutup <span aria-hidden="true">×</span></button>
    <img src="" alt="Preview bukti pembayaran ukuran penuh" id="proofLightboxImage">
</div>
@endsection
