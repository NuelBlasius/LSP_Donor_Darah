@extends('layouts.app')

@section('title', 'Verifikasi Pendaftaran | DonorDarah')
@section('page-title', 'Verifikasi Pendaftaran')

@section('content')
<div class="mb-4">
    <h1 class="h2 fw-bold mb-1">Verifikasi Pendaftaran Donor</h1>
    <p class="text-secondary mb-0">Periksa data pendonor dan verifikasi bukti pembayaran dari satu menu.</p>
</div>

<ul class="nav nav-pills verification-tabs mb-3" aria-label="Jenis verifikasi">
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'registrations' ? 'active' : '' }}" href="{{ route('admin.registrations.index', ['tab' => 'registrations']) }}">
            Verifikasi Pendaftaran
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'payments' ? 'active' : '' }}" href="{{ route('admin.registrations.index', ['tab' => 'payments']) }}">
            Verifikasi Pembayaran
        </a>
    </li>
</ul>

<form method="GET" action="{{ route('admin.registrations.index') }}" class="card border-0 shadow-sm p-3 mb-3">
    <input type="hidden" name="tab" value="{{ $activeTab }}">
    <div class="row g-2 align-items-end">
        <div class="col-md-6">
            <label for="search" class="form-label">Nama pendonor</label>
            <input id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari berdasarkan nama">
        </div>
        <div class="col-md-4">
            <label for="date" class="form-label">Tanggal donor</label>
            <input id="date" type="date" name="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-danger flex-grow-1" type="submit">Cari</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.registrations.index', ['tab' => $activeTab]) }}">Reset</a>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Pendonor</th>
                    <th>Jadwal</th>
                    <th>Tanggal</th>
                    <th>Nominal</th>
                    <th>Status pendaftaran</th>
                    <th>Status pembayaran</th>
                    <th><span class="visually-hidden">Detail</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($registrations as $registration)
                    @php
                        $person = $registration->user;
                        $modalId = 'registrationDetail' . $registration->id;
                        $registrationStatusText = $registration->registration_status === 'approved'
                            ? 'Disetujui'
                            : ($registration->registration_status === 'rejected' ? 'Ditolak' : 'Menunggu');
                        $registrationBadgeClass = $registration->registration_status === 'approved'
                            ? 'text-bg-success'
                            : ($registration->registration_status === 'rejected' ? 'text-bg-danger' : 'text-bg-warning');
                        $paymentStatusText = match ($registration->payment_status) {
                            'approved' => 'Terverifikasi',
                            'rejected' => 'Ditolak',
                            default => $registration->payment_proof ? 'Menunggu verifikasi' : 'Belum dibayar',
                        };
                        $paymentBadgeClass = match ($registration->payment_status) {
                            'approved' => 'text-bg-success',
                            'rejected' => 'text-bg-danger',
                            default => $registration->payment_proof ? 'text-bg-warning' : 'text-bg-secondary',
                        };
                    @endphp
                    <tr class="registration-row" tabindex="0" role="button" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}" aria-label="Buka detail pendaftaran {{ $person?->name ?? '' }}">
                        <td>
                            <div class="fw-semibold">{{ $person?->name ?? 'Akun tidak tersedia' }}</div>
                            <div class="small text-secondary">{{ $person?->email ?? '-' }}</div>
                        </td>
                        <td>{{ $registration->schedule?->title ?? 'Jadwal dihapus' }}</td>
                        <td>{{ $registration->schedule?->schedule_date?->format('d M Y') ?? '-' }}</td>
                        <td class="fw-semibold">Rp {{ number_format((int) ($registration->schedule?->amount ?? 0), 0, ',', '.') }}</td>
                        <td><span class="badge {{ $registrationBadgeClass }}">{{ $registrationStatusText }}</span></td>
                        <td><span class="badge {{ $paymentBadgeClass }}">{{ $paymentStatusText }}</span></td>
                        <td><span class="small text-danger fw-semibold">Lihat detail</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-secondary py-4">
                            {{ $activeTab === 'payments' ? 'Belum ada bukti pembayaran yang menunggu verifikasi.' : 'Belum ada pendaftaran donor yang menunggu verifikasi.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($registrations->hasPages())
        <div class="card-footer bg-white">{{ $registrations->links() }}</div>
    @endif
</div>

@foreach ($registrations as $registration)
    @php
        $person = $registration->user;
        $nik = $person?->nik ?? '-';
        $phone = $person?->nomor_telepon ?? '-';
        $maskedNik = $nik !== '-' ? str_repeat('•', max(0, strlen($nik) - 4)) . substr($nik, -4) : '-';
        $maskedPhone = $phone !== '-' ? str_repeat('•', max(0, strlen($phone) - 3)) . substr($phone, -3) : '-';
        $modalId = 'registrationDetail' . $registration->id;
        $paymentStatusText = match ($registration->payment_status) {
            'approved' => 'Terverifikasi',
            'rejected' => 'Ditolak',
            default => $registration->payment_proof ? 'Menunggu verifikasi' : 'Belum dibayar',
        };
        $paymentBadgeClass = match ($registration->payment_status) {
            'approved' => 'text-bg-success',
            'rejected' => 'text-bg-danger',
            default => $registration->payment_proof ? 'text-bg-warning' : 'text-bg-secondary',
        };
    @endphp
    <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <div>
                        <h2 class="modal-title h5 fw-bold" id="{{ $modalId }}Label">Detail Pendaftaran</h2>
                        <div class="small text-secondary">{{ $person?->name ?? '-' }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-lg-5">
                            <h3 class="h6 fw-bold mb-3">Informasi pendonor</h3>
                            <div class="detail-field"><div class="detail-label">Nama lengkap</div><div class="fw-semibold">{{ $person?->name ?? '-' }}</div></div>
                            <div class="detail-field"><div class="detail-label">Email</div><div>{{ $person?->email ?? '-' }}</div></div>
                            <div class="detail-field"><div class="detail-label">NIK</div><div class="d-flex align-items-center gap-2"><span class="sensitive-value" data-full="{{ $nik }}" data-masked="{{ $maskedNik }}">{{ $maskedNik }}</span><button type="button" class="btn btn-sm btn-outline-secondary reveal-sensitive" aria-label="Tampilkan NIK" data-revealed="false">&#128065;</button></div></div>
                            <div class="detail-field"><div class="detail-label">Nomor telepon</div><div class="d-flex align-items-center gap-2"><span class="sensitive-value" data-full="{{ $phone }}" data-masked="{{ $maskedPhone }}">{{ $maskedPhone }}</span><button type="button" class="btn btn-sm btn-outline-secondary reveal-sensitive" aria-label="Tampilkan nomor telepon" data-revealed="false">&#128065;</button></div></div>
                            <div class="detail-field"><div class="detail-label">Jenis kelamin / Golongan darah</div><div>{{ $person?->jenis_kelamin ?? '-' }} / {{ $person?->golongan_darah ?? '-' }}</div></div>
                            <div class="detail-field"><div class="detail-label">Tanggal lahir</div><div>{{ $person?->tanggal_lahir?->format('d M Y') ?? '-' }}</div></div>
                            <div class="detail-field"><div class="detail-label">Alamat</div><div>{{ $person?->alamat ?? '-' }}</div></div>
                        </div>
                        <div class="col-lg-7">
                            <h3 class="h6 fw-bold mb-3">Informasi pendaftaran dan pembayaran</h3>
                            <div class="row g-3">
                                <div class="col-sm-6"><div class="detail-field"><div class="detail-label">Jadwal donor</div><div class="fw-semibold">{{ $registration->schedule?->title ?? '-' }}</div></div></div>
                                <div class="col-sm-6">
                                    <div class="detail-field">
                                        <div class="detail-label">Lokasi</div>
                                        <div>
                                            @if ($registration->schedule?->map_url)
                                                <a class="map-location" href="{{ $registration->schedule->map_url }}" target="_blank" rel="noopener noreferrer">{{ $registration->schedule->map_address }}</a>
                                            @else
                                                {{ $registration->schedule?->map_address ?? '-' }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6"><div class="detail-field"><div class="detail-label">Tanggal dan waktu</div><div>{{ $registration->schedule?->schedule_date?->format('d M Y') ?? '-' }} · {{ $registration->schedule ? substr($registration->schedule->start_time, 0, 5) . '–' . substr($registration->schedule->end_time, 0, 5) : '-' }}</div></div></div>
                                <div class="col-sm-6"><div class="detail-field"><div class="detail-label">Nominal</div><div class="fw-bold text-danger">Rp {{ number_format((int) ($registration->schedule?->amount ?? 0), 0, ',', '.') }}</div></div></div>
                                <div class="col-sm-6"><div class="detail-field"><div class="detail-label">Metode pembayaran</div><div>{{ $registration->payment_method === 'virtual_account' ? 'Virtual Account' : ($registration->payment_method === 'qris' ? 'QRIS' : 'Belum dipilih') }}</div></div></div>
                                <div class="col-sm-6"><div class="detail-field"><div class="detail-label">Status pembayaran</div><span class="badge {{ $paymentBadgeClass }}">{{ $paymentStatusText }}</span></div></div>
                            </div>

                            <div class="detail-field mt-2">
                                <div class="detail-label mb-2">Bukti pembayaran</div>
                                @if ($registration->payment_proof)
                                    <button type="button" class="proof-preview-trigger border-0 p-0 bg-transparent" data-proof-src="{{ asset('storage/' . $registration->payment_proof) }}" aria-label="Buka preview bukti pembayaran ukuran penuh">
                                        <img src="{{ asset('storage/' . $registration->payment_proof) }}" class="img-fluid proof-preview rounded-3" alt="Bukti pembayaran dari {{ $person?->name ?? 'pendonor' }}">
                                    </button>
                                    <p class="small text-secondary mt-2 mb-0">Pilih gambar untuk membuka preview ukuran penuh yang dapat digulir.</p>
                                @else
                                    <div class="border rounded-3 p-4 text-secondary text-center">Pendonor belum mengunggah bukti pembayaran.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between flex-wrap gap-2">
                    <div class="d-flex flex-wrap gap-2">
                        @if ($registration->registration_status === 'pending')
                            <form method="POST" action="{{ route('admin.registrations.status', $registration) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="registration_status" value="approved">
                                <button class="btn btn-success" type="submit">Setujui pendaftaran</button>
                            </form>
                            <form method="POST" action="{{ route('admin.registrations.status', $registration) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="registration_status" value="rejected">
                                <button class="btn btn-outline-danger" type="submit">Tolak pendaftaran</button>
                            </form>
                        @endif
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @if ($registration->payment_proof && $registration->payment_status === 'pending')
                            <form method="POST" action="{{ route('admin.payments.status', $registration) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="payment_status" value="approved">
                                <button class="btn btn-primary" type="submit">Verifikasi bukti pembayaran</button>
                            </form>
                            <form method="POST" action="{{ route('admin.payments.status', $registration) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="payment_status" value="rejected">
                                <button class="btn btn-outline-danger" type="submit">Tolak bukti pembayaran</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

<div class="proof-lightbox" id="proofLightbox" role="dialog" aria-modal="true" aria-label="Preview bukti pembayaran" hidden>
    <button type="button" class="btn btn-light proof-lightbox-close" data-proof-close aria-label="Tutup preview">Tutup <span aria-hidden="true">×</span></button>
    <img src="" alt="Preview bukti pembayaran ukuran penuh" id="proofLightboxImage">
</div>
@endsection
