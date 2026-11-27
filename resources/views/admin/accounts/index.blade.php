@extends('layouts.app')

@section('title', 'Verifikasi Akun | DonorDarah')

@section('content')
<div class="mb-4">
    <h1 class="h2 fw-bold mb-1">Verifikasi Akun Pendonor</h1>
    <p class="text-secondary mb-0">Pilih baris pendonor untuk memeriksa profil sebelum menyetujui akun.</p>
</div>

<form method="GET" action="{{ route('admin.accounts.index') }}" class="card border-0 shadow-sm p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-6">
            <label for="search" class="form-label">Nama pendonor</label>
            <input id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari nama pendonor">
        </div>
        <div class="col-md-4">
            <label for="date" class="form-label">Tanggal registrasi akun</label>
            <input id="date" type="date" name="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-danger flex-grow-1" type="submit">Cari</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.accounts.index') }}">Reset</a>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Nama</th><th>Email</th><th>Registrasi akun</th><th>Status</th></tr>
            </thead>
            <tbody>
            @forelse ($users as $user)
                @php
                    $accountStatusText = 'Menunggu';
                    $accountBadgeClass = 'text-bg-warning';
                    if ($user->account_status === 'approved') {
                        $accountStatusText = 'Disetujui';
                        $accountBadgeClass = 'text-bg-success';
                    } elseif ($user->account_status === 'rejected') {
                        $accountStatusText = 'Ditolak';
                        $accountBadgeClass = 'text-bg-danger';
                    }
                    $nik = $user->nik ?? '-';
                    $phone = $user->nomor_telepon ?? '-';
                    $maskedNik = $nik !== '-' ? str_repeat('•', max(0, strlen($nik) - 4)) . substr($nik, -4) : '-';
                    $maskedPhone = $phone !== '-' ? str_repeat('•', max(0, strlen($phone) - 3)) . substr($phone, -3) : '-';
                    $modalId = 'accountDetail' . $user->id;
                @endphp
                <tr class="registration-row" tabindex="0" role="button" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
                    <td class="fw-semibold">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at?->format('d M Y, H:i') ?? '-' }}</td>
                    <td><span class="badge {{ $accountBadgeClass }}">{{ $accountStatusText }}</span></td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-secondary py-4">Belum ada akun pendonor yang sesuai pencarian.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($users->hasPages())
        <div class="card-footer bg-white">{{ $users->links() }}</div>
    @endif
</div>

@foreach ($users as $user)
    @php
        $nik = $user->nik ?? '-';
        $phone = $user->nomor_telepon ?? '-';
        $maskedNik = $nik !== '-' ? str_repeat('•', max(0, strlen($nik) - 4)) . substr($nik, -4) : '-';
        $maskedPhone = $phone !== '-' ? str_repeat('•', max(0, strlen($phone) - 3)) . substr($phone, -3) : '-';
        $modalId = 'accountDetail' . $user->id;
    @endphp
    <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <div><h2 class="modal-title h5 fw-bold" id="{{ $modalId }}Label">Profil Pendonor</h2><div class="small text-secondary">{{ $user->name }}</div></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-field"><div class="detail-label">Nama lengkap</div><div>{{ $user->name }}</div></div>
                            <div class="detail-field"><div class="detail-label">Email</div><div>{{ $user->email }}</div></div>
                            <div class="detail-field"><div class="detail-label">NIK</div><div class="d-flex align-items-center gap-2"><span class="sensitive-value" data-full="{{ $nik }}" data-masked="{{ $maskedNik }}">{{ $maskedNik }}</span><button type="button" class="btn btn-sm btn-outline-secondary reveal-sensitive" aria-label="Tampilkan data" data-revealed="false">&#128065;</button></div></div>
                            <div class="detail-field"><div class="detail-label">Nomor telepon</div><div class="d-flex align-items-center gap-2"><span class="sensitive-value" data-full="{{ $phone }}" data-masked="{{ $maskedPhone }}">{{ $maskedPhone }}</span><button type="button" class="btn btn-sm btn-outline-secondary reveal-sensitive" aria-label="Tampilkan data" data-revealed="false">&#128065;</button></div></div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-field"><div class="detail-label">Jenis kelamin</div><div>{{ $user->jenis_kelamin ?? '-' }}</div></div>
                            <div class="detail-field"><div class="detail-label">Golongan darah</div><div>{{ $user->golongan_darah ?? '-' }}</div></div>
                            <div class="detail-field"><div class="detail-label">Tanggal lahir</div><div>{{ $user->tanggal_lahir?->format('d M Y') ?? '-' }}</div></div>
                            <div class="detail-field"><div class="detail-label">Alamat</div><div>{{ $user->alamat ?? '-' }}</div></div>
                            <div class="detail-field"><div class="detail-label">Dibuat pada</div><div>{{ $user->created_at?->format('d M Y, H:i:s') ?? '-' }}</div></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <form method="POST" action="{{ route('admin.accounts.status', $user) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="account_status" value="approved">
                        <button class="btn btn-success" type="submit">Setujui akun</button>
                    </form>
                    <form method="POST" action="{{ route('admin.accounts.status', $user) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="account_status" value="rejected">
                        <button class="btn btn-outline-danger" type="submit">Tolak akun</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
