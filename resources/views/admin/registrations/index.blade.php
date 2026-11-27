@extends('layouts.app')

@section('title', 'Verifikasi Pendaftaran | DonorDarah')

@section('content')
<div class="mb-4">
    <h1 class="h2 fw-bold mb-1">Verifikasi Pendaftaran Donor</h1>
    <p class="text-secondary mb-0">Periksa data pendaftaran dan bukti pembayaran dari satu halaman.</p>
</div>

<form method="GET" action="{{ route('admin.registrations.index') }}" class="card border-0 shadow-sm p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-6">
            <label for="search" class="form-label">Nama pendonor</label>
            <input id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari berdasarkan nama">
        </div>
        <div class="col-md-4">
            <label for="date" class="form-label">Tanggal registrasi akun</label>
            <input id="date" type="date" name="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-danger flex-grow-1" type="submit">Cari</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.registrations.index') }}">Reset</a>
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
                </tr>
            </thead>
            <tbody>
            @forelse ($registrations as $registration)
                @php
                    $person = $registration->user;
                    $registrationStatusText = 'Menunggu';
                    $registrationBadgeClass = 'text-bg-warning';
                    if ($registration->registration_status === 'approved') {
                        $registrationStatusText = 'Disetujui';
                        $registrationBadgeClass = 'text-bg-success';
                    } elseif ($registration->registration_status === 'rejected') {
                        $registrationStatusText = 'Ditolak';
                        $registrationBadgeClass = 'text-bg-danger';
                    }

                    $paymentStatusText = 'Belum dibayar';
                    $paymentBadgeClass = 'text-bg-secondary';
                    if ($registration->payment_status === 'pending' && $registration->payment_proof) {
                        $paymentStatusText = 'Menunggu verifikasi';
                        $paymentBadgeClass = 'text-bg-warning';
                    } elseif ($registration->payment_status === 'approved') {
                        $paymentStatusText = 'Terverifikasi';
                        $paymentBadgeClass = 'text-bg-success';
                    } elseif ($registration->payment_status === 'rejected') {
                        $paymentStatusText = 'Ditolak';
                        $paymentBadgeClass = 'text-bg-danger';
                    }

                    $nik = $person?->nik ?? '-';
                    $phone = $person?->nomor_telepon ?? '-';
                    $maskedNik = $nik !== '-' ? str_repeat('•', max(0, strlen($nik) - 4)) . substr($nik, -4) : '-';
                    $maskedPhone = $phone !== '-' ? str_repeat('•', max(0, strlen($phone) - 3)) . substr($phone, -3) : '-';
                    $modalId = 'registrationDetail' . $registration->id;
                @endphp
                <tr class="registration-row" tabindex="0" role="button" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
                    <td>
                        <div class="fw-semibold">{{ $person?->name ?? 'Akun tidak tersedia' }}</div>
                        <div class="small text-secondary">{{ $person?->email ?? '-' }}</div>
                    </td>
                    <td>{{ $registration->schedule?->title ?? 'Jadwal dihapus' }}</td>
                    <td>{{ $registration->schedule?->schedule_date?->format('d M Y') ?? '-' }}</td>
                    <td class="fw-semibold">Rp {{ number_format((int) ($registration->schedule?->amount ?? 0), 0, ',', '.') }}</td>
                    <td><span class="badge {{ $registrationBadgeClass }}">{{ $registrationStatusText }}</span></td>
                    <td><span class="badge {{ $paymentBadgeClass }}">{{ $paymentStatusText }}</span></td>
                </tr>

            @empty
                <tr><td colspan="6" class="text-center text-secondary py-4">Belum ada pendaftaran donor yang sesuai pencarian.</td></tr>
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
        $paymentStatusText = 'Belum dibayar';
        $paymentBadgeClass = 'text-bg-secondary';
        if ($registration->payment_status === 'pending' && $registration->payment_proof) {
            $paymentStatusText = 'Menunggu verifikasi';
            $paymentBadgeClass = 'text-bg-warning';
        } elseif ($registration->payment_status === 'approved') {
            $paymentStatusText = 'Terverifikasi';
            $paymentBadgeClass = 'text-bg-success';
        } elseif ($registration->payment_status === 'rejected') {
            $paymentStatusText = 'Ditolak';
            $paymentBadgeClass = 'text-bg-danger';
        }
    @endphp
    <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <div><h2 class="modal-title h5 fw-bold" id="{{ $modalId }}Label">Detail Pendaftaran</h2><div class="small text-secondary">{{ $person?->name ?? '-' }}</div></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-field"><div class="detail-label">Nama lengkap</div><div class="fw-semibold">{{ $person?->name ?? '-' }}</div></div>
                            <div class="detail-field"><div class="detail-label">Email</div><div>{{ $person?->email ?? '-' }}</div></div>
                            <div class="detail-field"><div class="detail-label">NIK</div><div class="d-flex align-items-center gap-2"><span class="sensitive-value" data-full="{{ $nik }}" data-masked="{{ $maskedNik }}">{{ $maskedNik }}</span><button type="button" class="btn btn-sm btn-outline-secondary reveal-sensitive" aria-label="Tampilkan data" data-revealed="false">&#128065;</button></div></div>
                            <div class="detail-field"><div class="detail-label">Nomor telepon</div><div class="d-flex align-items-center gap-2"><span class="sensitive-value" data-full="{{ $phone }}" data-masked="{{ $maskedPhone }}">{{ $maskedPhone }}</span><button type="button" class="btn btn-sm btn-outline-secondary reveal-sensitive" aria-label="Tampilkan data" data-revealed="false">&#128065;</button></div></div>
                            <div class="detail-field"><div class="detail-label">Jenis kelamin / Golongan darah</div><div>{{ $person?->jenis_kelamin ?? '-' }} / {{ $person?->golongan_darah ?? '-' }}</div></div>
                            <div class="detail-field"><div class="detail-label">Tanggal lahir</div><div>{{ $person?->tanggal_lahir?->format('d M Y') ?? '-' }}</div></div>
                            <div class="detail-field"><div class="detail-label">Alamat</div><div>{{ $person?->alamat ?? '-' }}</div></div>
                            <div class="detail-field"><div class="detail-label">Registrasi akun</div><div>{{ $person?->created_at?->format('d M Y, H:i:s') ?? '-' }}</div></div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-field"><div class="detail-label">Jadwal donor</div><div class="fw-semibold">{{ $registration->schedule?->title ?? '-' }}</div></div>
                            <div class="detail-field"><div class="detail-label">Lokasi & tanggal</div><div>{{ $registration->schedule?->location ?? '-' }}<br>{{ $registration->schedule?->schedule_date?->format('d M Y') ?? '-' }}</div></div>
                            <div class="detail-field"><div class="detail-label">Nominal pembayaran</div><div class="h5 text-danger fw-bold">Rp {{ number_format((int) ($registration->schedule?->amount ?? 0), 0, ',', '.') }}</div></div>
                            <div class="detail-field"><div class="detail-label">Metode</div><div>{{ $registration->payment_method === 'virtual_account' ? 'Virtual Account (demo)' : ($registration->payment_method === 'qris' ? 'QRIS (demo)' : 'Belum dipilih') }}</div></div>
                            <div class="detail-field"><div class="detail-label">Status pembayaran</div><span class="badge {{ $paymentBadgeClass }}">{{ $paymentStatusText }}</span></div>
                            <div class="detail-field"><div class="detail-label mb-2">Bukti pembayaran</div>
                                @if ($registration->payment_proof)
                                    <a href="{{ asset('storage/' . $registration->payment_proof) }}" target="_blank" rel="noopener"><img src="{{ asset('storage/' . $registration->payment_proof) }}" class="img-fluid proof-preview rounded-3" alt="Bukti pembayaran pendonor"></a>
                                @else
                                    <div class="border rounded-3 p-4 text-secondary text-center">Pendonor belum mengunggah bukti pembayaran.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer flex-wrap justify-content-between">
                    <div class="d-flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.registrations.status', $registration) }}">@csrf @method('PATCH')
                            <input type="hidden" name="registration_status" value="approved"><button class="btn btn-success" type="submit">Setujui pendaftaran</button>
                        </form>
                        <form method="POST" action="{{ route('admin.registrations.status', $registration) }}">@csrf @method('PATCH')
                            <input type="hidden" name="registration_status" value="rejected"><button class="btn btn-outline-danger" type="submit">Tolak pendaftaran</button>
                        </form>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @if ($registration->payment_proof && $registration->payment_status !== 'approved')
                            <form method="POST" action="{{ route('admin.payments.status', $registration) }}">@csrf @method('PATCH')
                                <input type="hidden" name="payment_status" value="approved"><button class="btn btn-success" type="submit">Verifikasi pembayaran</button>
                            </form>
                            <form method="POST" action="{{ route('admin.payments.status', $registration) }}">@csrf @method('PATCH')
                                <input type="hidden" name="payment_status" value="rejected"><button class="btn btn-outline-danger" type="submit">Tolak pembayaran</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
