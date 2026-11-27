@extends('layouts.app')

@section('title', 'Pembayaran Pendaftaran | DonorDarah')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-10">
        <section class="card border-0 shadow-sm">
            <div class="card-header bg-white p-4">
                <h1 class="h4 fw-bold mb-1">Pembayaran Pendaftaran</h1>
                <p class="text-secondary mb-0">Pilih metode pembayaran demo, lalu unggah bukti transfer pada halaman ini.</p>
            </div>
            <div class="card-body p-4">
                <div class="payment-summary rounded-3 p-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <div>
                            <div class="small text-secondary">Total pembayaran</div>
                            <div class="h3 fw-bold text-danger mb-0">Rp {{ number_format((int) ($registration->schedule?->amount ?? 0), 0, ',', '.') }}</div>
                        </div>
                        <span class="badge text-bg-warning">Menunggu pembayaran</span>
                    </div>
                    <hr>
                    <div class="row g-2 small">
                        <div class="col-sm-6"><span class="text-secondary">Jadwal</span><div class="fw-semibold">{{ $registration->schedule?->title ?? '-' }}</div></div>
                        <div class="col-sm-6"><span class="text-secondary">Lokasi</span><div class="fw-semibold">{{ $registration->schedule?->location ?? '-' }}</div></div>
                        <div class="col-sm-6"><span class="text-secondary">Tanggal</span><div class="fw-semibold">{{ $registration->schedule?->schedule_date?->format('d M Y') ?? '-' }}</div></div>
                        <div class="col-sm-6"><span class="text-secondary">Kode pendaftaran</span><div class="fw-semibold">REG-{{ str_pad((string) $registration->id, 5, '0', STR_PAD_LEFT) }}</div></div>
                    </div>
                </div>

                @if ($registration->payment_proof && $registration->payment_status === 'pending')
                    <div class="alert alert-info">Bukti pembayaran Anda sudah dikirim dan sedang menunggu verifikasi admin.</div>
                @elseif ($registration->payment_status === 'rejected')
                    <div class="alert alert-warning">Bukti pembayaran sebelumnya ditolak. Silakan unggah bukti yang benar sebelum kode pembayaran kedaluwarsa.</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h6 fw-bold mb-0">Kode pembayaran berlaku selama</h2>
                    <span class="badge text-bg-danger fs-6 payment-countdown" data-expiry="{{ $registration->payment_expires_at->timestamp * 1000 }}">15:00</span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="payment-method-option h-100">
                            <input type="radio" name="payment_method" value="virtual_account" form="paymentUploadForm" checked>
                            <span>
                                <span class="d-block fw-bold">Virtual Account</span>
                                <span class="small text-secondary">Nomor VA dummy untuk simulasi</span>
                            </span>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="payment-method-option h-100">
                            <input type="radio" name="payment_method" value="qris" form="paymentUploadForm">
                            <span>
                                <span class="d-block fw-bold">QRIS</span>
                                <span class="small text-secondary">QR dummy untuk simulasi</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div id="virtualAccountPanel" class="payment-detail-panel rounded-3 p-3 mb-4">
                    <div class="small text-secondary mb-1">Nomor Virtual Account (dummy)</div>
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <strong id="virtualAccountNumber" class="fs-4">{{ $registration->payment_reference }}</strong>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="copyVirtualAccount">Salin nomor</button>
                    </div>
                    <p class="small text-secondary mb-0 mt-2">Nomor ini hanya untuk demonstrasi dan tidak dapat digunakan untuk transaksi bank sungguhan.</p>
                </div>

                <div id="qrisPanel" class="payment-detail-panel rounded-3 p-3 mb-4 d-none">
                    <div class="row align-items-center g-3">
                        <div class="col-sm-5 text-center">
                            <img src="{{ asset('images/qris-demo.svg') }}" class="qris-demo-image" alt="QRIS dummy demonstrasi">
                            <div class="small text-secondary mt-2">QRIS dummy — tidak dapat dipindai untuk pembayaran nyata</div>
                        </div>
                        <div class="col-sm-7">
                            <div class="small text-secondary">Nominal QRIS</div>
                            <div class="h4 fw-bold text-danger">Rp {{ number_format((int) ($registration->schedule?->amount ?? 0), 0, ',', '.') }}</div>
                            <div class="small text-secondary">Ref: {{ $registration->payment_reference }}</div>
                        </div>
                    </div>
                </div>

                <form id="paymentUploadForm" method="POST" action="{{ route('donor.registrations.payment.store', $registration) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="payment_proof" class="form-label fw-semibold">Unggah bukti pembayaran</label>
                        <input id="payment_proof" type="file" name="payment_proof" class="form-control" accept="image/jpeg,image/png,image/webp" required>
                        <div class="form-text">Format JPG, PNG, atau WebP. Maksimal 2 MB setelah optimasi.</div>
                        <div id="paymentProofPreview" class="small text-secondary mt-2" aria-live="polite"></div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-danger px-4">Kirim Bukti Pembayaran</button>
                        <a href="{{ route('donor.registrations.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
@endsection
