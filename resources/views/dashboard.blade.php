@extends('layouts.app')

@section('title', 'Dashboard - DonorDarah')

@section('content')
<div class="dashboard-layout">
    <aside class="dashboard-sidebar">
        <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 px-2 mb-4">
            <span class="brand-mark" style="background:#fff;color:var(--blood)">+</span>
            <span class="brand-name">DonorDarah</span>
        </a>

        <div class="sidebar-menu">
            <a href="{{ route('dashboard') }}" class="sidebar-link active"><span class="nav-symbol">⌂</span> Dashboard</a>
            <a href="{{ route('home') }}#jadwal" class="sidebar-link"><span class="nav-symbol">▦</span> Pendaftaran Donor</a>
            <a href="#riwayat" class="sidebar-link"><span class="nav-symbol">◷</span> Riwayat Donor</a>
            <a href="{{ route('profile.edit') }}" class="sidebar-link"><span class="nav-symbol">♙</span> Profil Saya</a>
        </div>

        <div class="sidebar-bottom">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-link border-0 bg-transparent w-100 text-start">
                    <span class="nav-symbol">↪</span> Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="dashboard-main">
        <header class="dashboard-topbar">
            <div>
                <h4 class="fw-bold mb-0">Dashboard</h4>
                <div class="small text-muted d-none d-sm-block">Ringkasan aktivitas donor Anda</div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted" title="Notifikasi">♧</span>
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-danger-subtle text-blood fw-bold d-grid place-items-center" style="width:38px;height:38px;text-align:center;line-height:38px">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="d-none d-sm-block">
                        <div class="fw-semibold small">{{ auth()->user()->name }}</div>
                        <div class="text-muted" style="font-size:.75rem">Pendonor</div>
                    </div>
                </div>
            </div>
        </header>

        <div class="dashboard-content">
            <section class="dashboard-welcome mb-4">
                <div class="position-relative" style="z-index:1">
                    <div class="small text-blood fw-bold mb-2">DASHBOARD PENDONOR</div>
                    <h2 class="mb-2">Halo, {{ auth()->user()->name }}! <span>👋</span></h2>
                    <p class="text-muted mb-0">Terima kasih telah menjadi bagian dari pendonor darah. Setiap kontribusi Anda berarti bagi sesama.</p>
                    <div class="mt-3">
                        @php
                            $accountStatus = auth()->user()->account_status ?? 'pending';
                            $accountLabel = ['pending' => 'Menunggu Verifikasi', 'approved' => 'Akun Terverifikasi', 'rejected' => 'Akun Ditolak'];
                            $accountClass = ['pending' => 'status-pending', 'approved' => 'status-approved', 'rejected' => 'status-rejected'];
                        @endphp
                        <span class="status-pill {{ $accountClass[$accountStatus] ?? 'status-other' }}">
                            Status akun: {{ $accountLabel[$accountStatus] ?? ucfirst($accountStatus) }}
                        </span>
                    </div>
                </div>
            </section>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-4">
                    <div class="stat-card">
                        <div class="stat-icon">▦</div>
                        <div><div class="stat-number">{{ $totalRegistrations }}</div><div class="stat-label">Total Pendaftaran</div></div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="stat-card">
                        <div class="stat-icon">◷</div>
                        <div><div class="stat-number">{{ $pendingRegistrations }}</div><div class="stat-label">Menunggu Verifikasi</div></div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <div class="stat-card">
                        <div class="stat-icon">♥</div>
                        <div><div class="stat-number">{{ $completedDonations }}</div><div class="stat-label">Donor Selesai</div></div>
                    </div>
                </div>
            </div>

            <section class="dashboard-panel" id="riwayat">
                <div class="dashboard-panel-head">
                    <div>
                        <h5 class="fw-bold mb-1">Pendaftaran Terbaru</h5>
                        <p class="text-muted small mb-0">Status aktivitas donor Anda</p>
                    </div>
                    <a href="{{ route('home') }}#jadwal" class="auth-link small">Lihat Jadwal <span>›</span></a>
                </div>
                <div class="dashboard-panel-body">
                    @if ($registrations->isEmpty())
                        <div class="text-center py-5">
                            <div class="feature-icon mx-auto mb-3">▦</div>
                            <h6 class="fw-bold">Belum ada pendaftaran</h6>
                            <p class="text-muted small mb-3">Saat ini Anda belum mendaftar jadwal donor darah.</p>
                            <a href="{{ route('home') }}#jadwal" class="btn btn-blood">Cari Jadwal Donor</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-dashboard">
                                <thead>
                                    <tr>
                                        <th>Tanggal Daftar</th>
                                        <th>Jadwal Donor</th>
                                        <th>Lokasi</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($registrations as $registration)
                                        @php
                                            $regStatus = $registration->registration_status ?? 'pending';
                                            $regLabel = ['pending' => 'Menunggu Verifikasi', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'completed' => 'Selesai'];
                                            $regClass = ['pending' => 'status-pending', 'approved' => 'status-approved', 'rejected' => 'status-rejected', 'completed' => 'status-approved'];
                                        @endphp
                                        <tr>
                                            <td>{{ \Illuminate\Support\Carbon::parse($registration->created_at)->format('d M Y') }}</td>
                                            <td>
                                                <div class="fw-semibold">{{ $registration->schedule_title }}</div>
                                                <div class="small text-muted">{{ \Illuminate\Support\Carbon::parse($registration->schedule_date)->format('d M Y') }}</div>
                                            </td>
                                            <td>{{ $registration->location }}</td>
                                            <td><span class="status-pill {{ $regClass[$regStatus] ?? 'status-other' }}">{{ $regLabel[$regStatus] ?? ucfirst($regStatus) }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
