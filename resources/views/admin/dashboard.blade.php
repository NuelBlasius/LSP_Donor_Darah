@extends('layouts.app')

@section('title', 'Dashboard Admin | DonorDarah')
@section('page-title', 'Dashboard Admin')

@section('content')
<div class="dashboard-wrap">
    <section class="admin-dashboard-hero mb-4">
        <div class="admin-dashboard-hero-copy">
            <span class="admin-dashboard-eyebrow">RUANG KERJA ADMIN</span>
            <h2 class="display-6 fw-bold mb-2">Selamat datang, {{ auth()->user()->name }}</h2>
            <p class="mb-0">Pantau pendaftaran, pembayaran, dan kegiatan donor dari satu tempat.</p>
        </div>
        <div class="admin-dashboard-hero-side">
            <span class="admin-dashboard-pulse" aria-hidden="true"></span>
            <div><span class="d-block small text-white-50">Jadwal donor aktif</span><strong>{{ $stats['open_schedules'] }}</strong></div>
            <a href="{{ route('admin.schedules.index') }}" class="btn btn-light btn-sm fw-semibold">Kelola jadwal <span aria-hidden="true">→</span></a>
        </div>
    </section>

    <div class="mb-3">
        <h3 class="h5 fw-bold mb-1">Ringkasan pengelolaan</h3>
        <p class="small text-secondary mb-0">Tindakan yang dapat dikerjakan hari ini.</p>
    </div>

    <div class="row g-3">
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('admin.accounts.index') }}" class="admin-stat-card admin-stat-card-red text-decoration-none h-100">
                <div class="admin-card-top"><span class="stat-icon stat-icon-red">01</span><span class="small text-secondary">AKUN</span></div>
                <div class="small text-secondary mt-3">Akun menunggu verifikasi</div><div class="h2 fw-bold text-dark my-2">{{ $stats['pending_accounts'] }}</div><span class="link-danger small fw-semibold">Periksa akun <span aria-hidden="true">→</span></span>
            </a>
        </div>
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('admin.schedules.index', ['queue' => 'registrations']) }}" class="admin-stat-card admin-stat-card-yellow text-decoration-none h-100">
                <div class="admin-card-top"><span class="stat-icon stat-icon-yellow">02</span><span class="small text-secondary">PENDAFTARAN</span></div>
                <div class="small text-secondary mt-3">Pendaftaran menunggu</div><div class="h2 fw-bold text-dark my-2">{{ $stats['pending_registrations'] }}</div><span class="link-danger small fw-semibold">Buka jadwal <span aria-hidden="true">→</span></span>
            </a>
        </div>
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('admin.schedules.index', ['queue' => 'payments']) }}" class="admin-stat-card admin-stat-card-green text-decoration-none h-100">
                <div class="admin-card-top"><span class="stat-icon stat-icon-green">03</span><span class="small text-secondary">PEMBAYARAN</span></div>
                <div class="small text-secondary mt-3">Bukti pembayaran menunggu</div><div class="h2 fw-bold text-dark my-2">{{ $stats['pending_payments'] }}</div><span class="link-danger small fw-semibold">Periksa bukti <span aria-hidden="true">→</span></span>
            </a>
        </div>
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('admin.schedules.index') }}" class="admin-stat-card admin-stat-card-blue text-decoration-none h-100">
                <div class="admin-card-top"><span class="stat-icon stat-icon-blue">04</span><span class="small text-secondary">JADWAL</span></div>
                <div class="small text-secondary mt-3">Jadwal donor aktif</div><div class="h2 fw-bold text-dark my-2">{{ $stats['open_schedules'] }}</div><span class="link-danger small fw-semibold">Kelola jadwal <span aria-hidden="true">→</span></span>
            </a>
        </div>
    </div>
</div>
@endsection
