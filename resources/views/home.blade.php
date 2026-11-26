@extends('layouts.app')

@section('title', 'Beranda - DonorDarah')

@section('content')
<div class="home-wrap">
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <div class="hero-kicker mb-4"><span>♥</span> Setetes darah, harapan bagi sesama</div>
                    <h1 class="hero-title mb-4">Ayo <span>Donor Darah,</span><br>Selamatkan Banyak Nyawa</h1>
                    <p class="hero-copy mb-4">
                        Bergabunglah menjadi pendonor dan bantu memenuhi kebutuhan darah bagi mereka yang membutuhkan.
                        Mulai dari registrasi hingga memilih jadwal donor, semuanya lebih mudah secara online.
                    </p>
                    <div class="d-flex flex-wrap gap-3" id="pendaftaran">
                        <a href="{{ route('register') }}" class="btn btn-blood btn-lg px-4 py-3">Daftar Donor Sekarang <span class="ms-2">›</span></a>
                        <a href="#informasi" class="btn btn-outline-blood btn-lg px-4 py-3">Pelajari Lebih Lanjut</a>
                    </div>
                    <div class="d-flex align-items-center gap-3 mt-4 text-muted small">
                        <span class="text-blood fw-bold">✓</span> Pendaftaran mudah
                        <span class="text-blood fw-bold">✓</span> Jadwal terorganisir
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-visual">
                        <div class="visual-circle"></div>
                        <div class="floating-note note-top"><span class="text-blood">♥</span> Donor itu berarti <small>Berbagi kehidupan</small></div>
                        <img class="donor-illustration" src="{{ asset('images/donor-hero.svg') }}" alt="Ilustrasi seseorang sedang melakukan donor darah">
                        <div class="floating-note note-bottom"><span class="text-blood">●</span> Jadilah pahlawan hari ini <small>Mulai dari satu langkah</small></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="benefits">
        <div class="container">
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="benefit-item">
                        <div class="benefit-icon">♡</div>
                        <div><strong>Bantu Sesama</strong><div class="small text-muted mt-1">Setiap kantong darah sangat berarti.</div></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="benefit-item">
                        <div class="benefit-icon">♧</div>
                        <div><strong>Proses Aman</strong><div class="small text-muted mt-1">Dilakukan oleh tenaga medis profesional.</div></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="benefit-item">
                        <div class="benefit-icon">▦</div>
                        <div><strong>Jadwal Terjadwal</strong><div class="small text-muted mt-1">Pilih waktu donor sesuai ketersediaan.</div></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<section class="py-5" id="informasi">
    <div class="container py-lg-3">
        <div class="text-center mb-4">
            <div class="hero-kicker mb-3">Layanan DonorDarah</div>
            <h2 class="section-title">Donor darah jadi lebih mudah</h2>
            <p class="text-muted">Kelola proses donor Anda melalui satu platform.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4" id="jadwal">
                <div class="feature-card">
                    <div class="feature-icon mb-3">▦</div>
                    <h5 class="fw-bold">Jadwal Donor</h5>
                    <p class="text-muted mb-0">Temukan jadwal dan lokasi kegiatan donor yang tersedia.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon mb-3">✓</div>
                    <h5 class="fw-bold">Pendaftaran Terpantau</h5>
                    <p class="text-muted mb-0">Pantau status pendaftaran dan konfirmasi melalui akun Anda.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon mb-3">♥</div>
                    <h5 class="fw-bold">Riwayat Donor</h5>
                    <p class="text-muted mb-0">Simpan informasi kegiatan donor yang pernah Anda ikuti.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="site-footer">
    <div class="container d-flex flex-wrap justify-content-between gap-2">
        <span class="fw-bold text-dark">DonorDarah</span>
        <span>Berbagi darah, berbagi harapan.</span>
    </div>
</footer>
@endsection
