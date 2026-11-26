@extends('layouts.app')

@section('title', 'Konfirmasi Pembayaran | DonorDarah')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3"><h1 class="h4 fw-bold mb-0">Konfirmasi Pembayaran</h1></div>
            <div class="card-body p-4">
                <div class="alert alert-info">Pendaftaran Anda telah disetujui. Unggah bukti pembayaran agar dapat diperiksa admin.</div>
                <dl class="row">
                    <dt class="col-sm-4">Jadwal</dt><dd class="col-sm-8">{{ $registration->schedule?->title }}</dd>
                    <dt class="col-sm-4">Lokasi</dt><dd class="col-sm-8">{{ $registration->schedule?->location }}</dd>
                    <dt class="col-sm-4">Tanggal</dt><dd class="col-sm-8">{{ $registration->schedule?->schedule_date?->format('d M Y') }}</dd>
                </dl>
                @if ($errors->any())<div class="alert alert-danger">@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
                <form method="POST" action="{{ route('donor.registrations.payment.store', $registration) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="payment_proof" class="form-label">Bukti Pembayaran (JPG, PNG, atau PDF; maksimal 3 MB)</label>
                        <input id="payment_proof" type="file" name="payment_proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                    </div>
                    <button type="submit" class="btn btn-danger">Kirim Bukti Pembayaran</button>
                    <a href="{{ route('donor.registrations.index') }}" class="btn btn-outline-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
