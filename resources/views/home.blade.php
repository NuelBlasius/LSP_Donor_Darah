@extends('layouts.app')

@section('title', 'Beranda - Donor Darah')

@section('content')

<div class="p-5 mb-4 bg-light rounded-3">
    <div class="container-fluid py-4">
        <h1 class="display-5 fw-bold">
            Donor Darah, Selamatkan Sesama
        </h1>

        <p class="col-md-8 fs-5">
            Bergabunglah menjadi pendonor dan bantu
            menyelamatkan kehidupan melalui donor darah.
        </p>

        <a href="/register" class="btn btn-danger btn-lg">
            Daftar sebagai Pendonor
        </a>
    </div>
</div>

<h2 class="mb-3">Informasi Donor Darah</h2>

<div class="row">

    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Registrasi Pendonor</h5>
                <p class="card-text">
                    Buat akun untuk bergabung sebagai pendonor darah.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Jadwal Donor</h5>
                <p class="card-text">
                    Lihat jadwal donor darah dan daftarkan diri
                    pada jadwal yang tersedia.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Pengumuman</h5>
                <p class="card-text">
                    Dapatkan informasi terbaru mengenai
                    kegiatan donor darah.
                </p>
            </div>
        </div>
    </div>

</div>

@endsection
