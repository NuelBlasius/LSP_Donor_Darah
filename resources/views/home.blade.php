@extends('layouts.app')

@section('title', 'Beranda | DonorDarah')

@section('content')
<section id="beranda" class="landing-hero rounded-4 overflow-hidden mb-4 landing-section">
    <div class="row align-items-center g-0">
        <div class="col-lg-6 p-4 p-lg-5">
            <span class="badge rounded-pill text-bg-danger mb-3">Setetes Darah, Harapan Baru</span>
            <h1 class="display-5 fw-bold text-dark mb-3">
                Ayo <span class="text-danger">Donor Darah</span>,<br>
                Selamatkan Banyak Nyawa
            </h1>
            <p class="lead text-secondary mb-4">
                Temukan informasi kegiatan donor, ajukan pendaftaran, dan pantau prosesnya dari satu akun.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('register') }}" class="btn btn-danger px-4 py-2">
                    Daftar Donor Sekarang <span aria-hidden="true">›</span>
                </a>
                <a href="#layanan" class="btn btn-outline-secondary px-4 py-2">
                    Pelajari Layanan
                </a>
            </div>
        </div>
        <div class="col-lg-6 hero-illustration">
            <img
                src="{{ asset('images/landing-donor.png') }}"
                class="img-fluid content-illustration"
                alt="Ilustrasi utama donor darah"
            >
        </div>
    </div>
</section>

<section class="feature-strip rounded-4 bg-white shadow-sm p-3 p-lg-4 mb-5" aria-label="Manfaat donor darah">
    <div class="row g-3">
        <div class="col-md-4">
            <div class="d-flex align-items-center gap-3 h-100 feature-item">
                <span class="feature-icon" aria-hidden="true">♡</span>
                <div>
                    <h2 class="h6 fw-bold mb-1">Bantu sesama</h2>
                    <p class="small text-secondary mb-0">Donor darah membantu menjaga ketersediaan darah bagi yang membutuhkan.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex align-items-center gap-3 h-100 feature-item">
                <span class="feature-icon" aria-hidden="true">✓</span>
                <div>
                    <h2 class="h6 fw-bold mb-1">Proses terarah</h2>
                    <p class="small text-secondary mb-0">Ikuti langkah pendaftaran dan pemeriksaan yang dikelola oleh admin.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex align-items-center gap-3 h-100 feature-item">
                <span class="feature-icon" aria-hidden="true">◷</span>
                <div>
                    <h2 class="h6 fw-bold mb-1">Status mudah dipantau</h2>
                    <p class="small text-secondary mb-0">Periksa perkembangan pendaftaran melalui dashboard pendonor.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="donor-motion-section mb-5" aria-labelledby="donor-motion-title">
    <div class="text-center mb-4">
        <h2 id="donor-motion-title" class="h2 fw-bold">Setetes Darah, Gerakan Penuh Harapan</h2>
        <p class="text-secondary mb-0">Ilustrasi bergerak tentang kepedulian, donor, dan kehidupan.</p>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <article class="donor-motion-card h-100">
                <div class="motion-art" aria-hidden="true">
                    <svg class="motion-drop" viewBox="0 0 120 140" role="presentation">
                        <path d="M60 8C48 32 20 62 20 91a40 40 0 0 0 80 0C100 62 72 32 60 8Z" fill="currentColor" />
                        <path d="M42 91a18 18 0 0 0 18 18" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="7" opacity=".75" />
                    </svg>
                </div>
                <h3 class="h5 fw-bold">Kepedulian dimulai dari Anda</h3>
                <p class="text-secondary mb-0">Satu keputusan sederhana dapat menjadi dukungan berarti bagi sesama.</p>
            </article>
        </div>
        <div class="col-md-4">
            <article class="donor-motion-card h-100">
                <div class="motion-art" aria-hidden="true">
                    <svg class="motion-heart" viewBox="0 0 140 120" role="presentation">
                        <path d="M70 108 18 59C-8 32 28-5 58 20l12 12 12-12c30-25 66 12 40 39Z" fill="currentColor" />
                        <path class="motion-pulse-line" d="M20 64h26l10-18 17 37 14-27 9 8h24" fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="6" />
                    </svg>
                </div>
                <h3 class="h5 fw-bold">Dukungan yang mengalir</h3>
                <p class="text-secondary mb-0">Petugas akan memandu proses donor dan memastikan setiap tahap dilakukan dengan tertib.</p>
            </article>
        </div>
        <div class="col-md-4">
            <article class="donor-motion-card h-100">
                <div class="motion-art" aria-hidden="true">
                    <svg class="motion-bag" viewBox="0 0 120 140" role="presentation">
                        <path d="M48 8h24v14H48zM42 22h36a12 12 0 0 1 12 12v72a14 14 0 0 1-14 14H44a14 14 0 0 1-14-14V34a12 12 0 0 1 12-12Z" fill="#fff" stroke="currentColor" stroke-width="6" />
                        <path class="motion-blood-level" d="M34 70h52v36a8 8 0 0 1-8 8H42a8 8 0 0 1-8-8Z" fill="currentColor" />
                        <path d="M60 73c-7 10-11 15-11 21a11 11 0 0 0 22 0c0-6-4-11-11-21Z" fill="#fff" />
                        <path d="M54 120v12m12-12v12" stroke="currentColor" stroke-linecap="round" stroke-width="6" />
                    </svg>
                </div>
                <h3 class="h5 fw-bold">Harapan untuk kehidupan</h3>
                <p class="text-secondary mb-0">Darah yang didonorkan membantu menjaga ketersediaan bagi pasien yang membutuhkan.</p>
            </article>
        </div>
    </div>
</section>

<section id="informasi" class="mb-5 landing-section">
    <div class="text-center mb-4">
        <h2 class="h2 fw-bold">Donor Darah, Mulai dari Sini</h2>
        <p class="text-secondary">Beberapa hal yang membantu Anda menjalani proses pendaftaran dengan lancar.</p>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <article class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <span class="badge text-bg-danger mb-3">01</span>
                    <h3 class="h5 fw-bold">Cari kegiatan yang sesuai</h3>
                    <p class="text-secondary mb-0">Periksa tanggal, lokasi, waktu, dan kuota sebelum memilih kegiatan donor.</p>
                </div>
            </article>
        </div>
        <div class="col-md-4">
            <article class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <span class="badge text-bg-danger mb-3">02</span>
                    <h3 class="h5 fw-bold">Lengkapi profil Anda</h3>
                    <p class="text-secondary mb-0">Pastikan informasi identitas dan kontak di akun sudah benar untuk diperiksa pengelola.</p>
                </div>
            </article>
        </div>
        <div class="col-md-4">
            <article class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <span class="badge text-bg-danger mb-3">03</span>
                    <h3 class="h5 fw-bold">Pantau perkembangan</h3>
                    <p class="text-secondary mb-0">Lihat status akun, pendaftaran, dan pembayaran melalui dashboard pendonor.</p>
                </div>
            </article>
        </div>
    </div>
</section>

<section id="layanan" class="landing-services rounded-4 p-4 p-lg-5 mb-5 landing-section">
    <div class="row align-items-center g-4">
        <div class="col-lg-5">
            <h2 class="h2 fw-bold">Kelola proses donor dari satu akun</h2>
            <p class="text-secondary mb-4">Website ini membantu Anda menemukan informasi kegiatan dan mengikuti proses pendaftaran. Jadwal tersedia dapat dilihat setelah masuk ke akun pendonor.</p>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('login') }}" class="btn btn-danger px-4">Masuk untuk melihat jadwal</a>
                <a href="{{ route('register') }}" class="btn btn-outline-secondary px-4">Buat akun</a>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="row g-3">
                <div class="col-md-4">
                    <article class="service-benefit h-100">
                        <h3 class="h6 fw-bold">Informasi kegiatan</h3>
                        <p class="small text-secondary mb-0">Lihat jadwal, lokasi, dan ketersediaan kuota di dashboard.</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="service-benefit h-100">
                        <h3 class="h6 fw-bold">Pendaftaran online</h3>
                        <p class="small text-secondary mb-0">Ajukan pendaftaran kegiatan tanpa mengisi ulang data profil.</p>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="service-benefit h-100">
                        <h3 class="h6 fw-bold">Riwayat terpusat</h3>
                        <p class="small text-secondary mb-0">Pantau hasil pemeriksaan dan pembayaran di satu tempat.</p>
                    </article>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
