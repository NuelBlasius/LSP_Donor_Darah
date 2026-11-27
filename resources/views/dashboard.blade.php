@extends('layouts.app')

@section('title', 'Dashboard Pendonor | DonorDarah')
@section('page-title', 'Dashboard')

@section('content')
@php
    $accountBadgeClass = 'text-bg-warning';
    $accountBadgeText = 'Menunggu Verifikasi';

    if ($user->account_status === 'approved') {
        $accountBadgeClass = 'text-bg-success';
        $accountBadgeText = 'Terverifikasi';
    } elseif ($user->account_status === 'rejected') {
        $accountBadgeClass = 'text-bg-danger';
        $accountBadgeText = 'Ditolak';
    }
@endphp

<div class="dashboard-wrap">
    <section class="dashboard-welcome rounded-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <div class="small text-danger fw-semibold mb-2">DASHBOARD PENDONOR</div>
                <h2 class="h3 fw-bold mb-2">
                    Halo, {{ $user->name }}!
                    <span aria-hidden="true">👋</span>
                </h2>
                <p class="text-secondary mb-3">
                    Terima kasih telah menjadi bagian dari pendonor darah. Pantau status akun dan pendaftaran Anda di sini.
                </p>
                <a href="{{ route('services') }}" class="btn btn-danger">
                    Cari Jadwal Donor <span aria-hidden="true">→</span>
                </a>
            </div>

            <div class="col-lg-4 text-center d-none d-lg-block">
                <img
                    src="{{ asset('images/donor-illustration.svg') }}"
                    class="welcome-art"
                    alt=""
                >
            </div>
        </div>
    </section>

    <section class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 p-lg-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="h5 fw-bold mb-1">Status Verifikasi Akun</h2>
                @if ($user->account_status === 'approved')
                    <p class="text-secondary mb-0">
                        Akun Anda telah disetujui. Anda dapat mendaftar jadwal donor yang tersedia.
                    </p>
                @elseif ($user->account_status === 'rejected')
                    <p class="text-secondary mb-0">
                        Akun belum disetujui. Silakan hubungi admin untuk informasi lebih lanjut.
                    </p>
                @else
                    <p class="text-secondary mb-0">
                        Admin sedang memeriksa akun Anda. Pendaftaran jadwal tersedia setelah akun disetujui.
                    </p>
                @endif
            </div>

            <span class="badge fs-6 px-3 py-2 {{ $accountBadgeClass }}">
                {{ $accountBadgeText }}
            </span>
        </div>
    </section>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card h-100">
                <div class="stat-icon stat-icon-red">▦</div>
                <div>
                    <div class="h3 fw-bold mb-0">{{ $totalRegistrations }}</div>
                    <div class="small text-secondary">Total Pendaftaran</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card h-100">
                <div class="stat-icon stat-icon-yellow">◷</div>
                <div>
                    <div class="h3 fw-bold mb-0">{{ $pendingRegistrations }}</div>
                    <div class="small text-secondary">Menunggu Verifikasi</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card h-100">
                <div class="stat-icon stat-icon-green">✓</div>
                <div>
                    <div class="h3 fw-bold mb-0">{{ $pendingPayments }}</div>
                    <div class="small text-secondary">Pembayaran Menunggu</div>
                </div>
            </div>
        </div>
    </div>

    <section class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h2 class="h5 fw-bold mb-0">Notifikasi</h2>
        </div>
        <div class="card-body">
            @forelse ($notifications as $notification)
                <article class="notification-item">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <h3 class="h6 fw-bold mb-1">{{ $notification->title }}</h3>
                        <span class="small text-secondary">{{ $notification->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <p class="text-secondary mb-0">{{ $notification->message }}</p>
                </article>
            @empty
                <p class="text-secondary mb-0">Belum ada notifikasi. Informasi status pendaftaran akan muncul di sini.</p>
            @endforelse
        </div>
    </section>

    <section class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h2 class="h5 fw-bold mb-0">Pendaftaran Terbaru</h2>
            <a href="{{ route('donor.registrations.index') }}" class="link-danger fw-semibold small">
                Lihat Riwayat →
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Jadwal Donor</th>
                        <th>Tanggal</th>
                        <th>Status Pendaftaran</th>
                        <th>Status Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($registrations as $registration)
                        @php
                            $registrationBadgeClass = 'text-bg-warning';
                            $registrationStatusText = 'Menunggu';
                            $paymentBadgeClass = 'text-bg-secondary';
                            $paymentStatusText = 'Menunggu';

                            if ($registration->registration_status === 'approved') {
                                $registrationBadgeClass = 'text-bg-success';
                                $registrationStatusText = 'Disetujui';
                            } elseif ($registration->registration_status === 'rejected') {
                                $registrationBadgeClass = 'text-bg-danger';
                                $registrationStatusText = 'Ditolak';
                            }

                            if ($registration->payment_status === 'approved') {
                                $paymentBadgeClass = 'text-bg-success';
                                $paymentStatusText = 'Disetujui';
                            } elseif ($registration->payment_status === 'rejected') {
                                $paymentBadgeClass = 'text-bg-danger';
                                $paymentStatusText = 'Ditolak';
                            }
                        @endphp
                        <tr>
                            <td class="fw-semibold">
                                {{ $registration->schedule?->title ?? 'Jadwal dihapus' }}
                            </td>
                            <td>
                                {{ $registration->schedule?->schedule_date?->format('d M Y') ?? '-' }}
                            </td>
                            <td>
                                <span class="badge {{ $registrationBadgeClass }}">
                                    {{ $registrationStatusText }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $paymentBadgeClass }}">
                                    {{ $paymentStatusText }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-secondary py-4">
                                Belum ada pendaftaran. Gunakan menu Layanan Donor untuk melihat jadwal yang tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <h2 class="h5 fw-bold mb-0">Pengumuman</h2>
            <p class="small text-secondary mb-0">Informasi terbaru dari pengelola donor.</p>
        </div>

        <div class="card-body">
            <div class="row g-3">
                @forelse ($announcements as $announcement)
                    <div class="col-md-6">
                        <article class="announcement-card h-100">
                            <div class="small text-danger fw-semibold">
                                {{ $announcement->created_at->format('d M Y') }}
                            </div>
                            <h3 class="h6 fw-bold mt-2">{{ $announcement->title }}</h3>
                            <p class="text-secondary mb-0">{{ $announcement->content }}</p>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-secondary mb-0">Belum ada pengumuman.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
