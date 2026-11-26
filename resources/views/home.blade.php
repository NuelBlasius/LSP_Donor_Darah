@extends('layouts.app')

@section('title', 'Beranda | DonorDarah')

@section('content')
<section class="landing-hero rounded-4 overflow-hidden mb-4">
    <div class="row align-items-center g-0">
        <div class="col-lg-6 p-4 p-lg-5">
            <span class="badge rounded-pill text-bg-danger mb-3">Setetes Darah, Harapan Baru</span>
            <h1 class="display-5 fw-bold text-dark mb-3">Ayo <span class="text-danger">Donor Darah</span>,<br>Selamatkan Banyak Nyawa</h1>
            <p class="lead text-secondary mb-4">Bergabunglah menjadi pendonor dan bantu memenuhi kebutuhan darah bagi mereka yang membutuhkan.</p>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('register') }}" class="btn btn-danger px-4 py-2">Daftar Donor Sekarang <span aria-hidden="true">›</span></a>
                <a href="{{ route('services') }}" class="btn btn-outline-secondary px-4 py-2">Pelajari Layanan</a>
            </div>
        </div>
        <div class="col-lg-6 hero-illustration">
            <img src="{{ asset('images/donor-illustration.svg') }}" class="img-fluid" alt="Ilustrasi pendonor sedang mendonorkan darah">
        </div>
    </div>
</section>

<section class="feature-strip rounded-4 bg-white shadow-sm p-3 p-lg-4 mb-5">
    <div class="row g-3">
        <div class="col-md-4"><div class="d-flex align-items-center gap-3 h-100 feature-item"><span class="feature-icon">♡</span><div><h2 class="h6 fw-bold mb-1">Bantu Sesama</h2><p class="small text-secondary mb-0">Setiap kantong darah sangat berarti.</p></div></div></div>
        <div class="col-md-4"><div class="d-flex align-items-center gap-3 h-100 feature-item"><span class="feature-icon">♧</span><div><h2 class="h6 fw-bold mb-1">Proses Terverifikasi</h2><p class="small text-secondary mb-0">Akun dan pendaftaran diperiksa admin.</p></div></div></div>
        <div class="col-md-4"><div class="d-flex align-items-center gap-3 h-100 feature-item"><span class="feature-icon">▦</span><div><h2 class="h6 fw-bold mb-1">Jadwal Teratur</h2><p class="small text-secondary mb-0">Pilih jadwal sesuai ketersediaan.</p></div></div></div>
    </div>
</section>

<section id="informasi" class="mb-5">
    <div class="text-center mb-4"><h2 class="h2 fw-bold">Donor Darah, Mulai dari Sini</h2><p class="text-secondary">Daftar akun, verifikasi, lalu pilih jadwal donor yang tersedia.</p></div>
    <div class="row justify-content-center g-3">
        <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body p-4"><span class="badge text-bg-danger mb-3">01</span><h3 class="h5 fw-bold">Buat Akun</h3><p class="text-secondary mb-0">Isi data diri dan nomor telepon untuk membuat akun pendonor.</p></div></div></div>
        <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body p-4"><span class="badge text-bg-danger mb-3">02</span><h3 class="h5 fw-bold">Verifikasi Akun</h3><p class="text-secondary mb-0">Tunggu pemeriksaan akun oleh pengelola sebelum mendaftar jadwal.</p></div></div></div>
        <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body p-4"><span class="badge text-bg-danger mb-3">03</span><h3 class="h5 fw-bold">Pilih Jadwal</h3><p class="text-secondary mb-0">Lihat layanan, daftar kegiatan, dan pantau status pendaftaran.</p></div></div></div>
    </div>
</section>
@endsection
