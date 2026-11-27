@extends('layouts.app')

@section('title', 'Dashboard Admin | DonorDarah')
@section('page-title', 'Dashboard Admin')

@section('content')
<div class="dashboard-wrap">
    <div class="mb-4">
        <span class="badge text-bg-danger rounded-pill mb-2">PANEL PENGELOLA</span>
        <h2 class="h3 fw-bold mb-1">Selamat datang, {{ auth()->user()->name }}</h2>
        <p class="text-secondary mb-0">Pantau proses verifikasi dan kelola kegiatan donor darah dari satu tempat.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('admin.accounts.index') }}" class="admin-stat-card text-decoration-none h-100">
                <div class="stat-icon stat-icon-red">♙</div><div class="small text-secondary">Akun Menunggu Verifikasi</div><div class="h2 fw-bold text-dark my-2">{{ $stats['pending_accounts'] }}</div><span class="link-danger small fw-semibold">Periksa akun →</span>
            </a>
        </div>
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('admin.registrations.index', ['tab' => 'registrations']) }}" class="admin-stat-card text-decoration-none h-100">
                <div class="stat-icon stat-icon-yellow">▣</div><div class="small text-secondary">Pendaftaran Menunggu</div><div class="h2 fw-bold text-dark my-2">{{ $stats['pending_registrations'] }}</div><span class="link-danger small fw-semibold">Verifikasi →</span>
            </a>
        </div>
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('admin.registrations.index', ['tab' => 'payments']) }}" class="admin-stat-card text-decoration-none h-100">
                <div class="stat-icon stat-icon-green">▤</div><div class="small text-secondary">Pembayaran Menunggu</div><div class="h2 fw-bold text-dark my-2">{{ $stats['pending_payments'] }}</div><span class="link-danger small fw-semibold">Periksa bukti →</span>
            </a>
        </div>
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('admin.schedules.index') }}" class="admin-stat-card text-decoration-none h-100">
                <div class="stat-icon stat-icon-red">▦</div><div class="small text-secondary">Jadwal Donor Aktif</div><div class="h2 fw-bold text-dark my-2">{{ $stats['open_schedules'] }}</div><span class="link-danger small fw-semibold">Kelola jadwal →</span>
            </a>
        </div>
    </div>

</div>
@endsection
