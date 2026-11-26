@extends('layouts.app')

@section('title', 'Verifikasi Pendaftaran | DonorDarah')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h1 class="h2 fw-bold">Verifikasi Pendaftaran Donor</h1><p class="text-secondary mb-0">Tinjau pendaftaran pendonor untuk setiap jadwal.</p></div>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Pendonor</th><th>Jadwal</th><th>Tanggal</th><th>Status</th><th>Tindakan</th></tr></thead>
            <tbody>
            @forelse ($registrations as $registration)
                @php
                    $registrationBadgeClass = 'text-bg-warning';
                    $registrationStatusText = 'Menunggu';

                    if ($registration->registration_status === 'approved') {
                        $registrationBadgeClass = 'text-bg-success';
                        $registrationStatusText = 'Disetujui';
                    } elseif ($registration->registration_status === 'rejected') {
                        $registrationBadgeClass = 'text-bg-danger';
                        $registrationStatusText = 'Ditolak';
                    }
                @endphp
                <tr>
                    <td>{{ $registration->user?->name }}<div class="small text-secondary">{{ $registration->user?->email }}</div><div class="small text-secondary">Telp: {{ $registration->user?->nomor_telepon ?? '-' }} · NIK: {{ $registration->user?->nik ?? '-' }}</div><div class="small text-secondary">Golongan: {{ $registration->user?->golongan_darah ?? '-' }} · Usia: {{ $registration->user?->tanggal_lahir?->age ?? '-' }} tahun</div></td>
                    <td>{{ $registration->schedule?->title }}</td><td>{{ $registration->schedule?->schedule_date?->format('d M Y') }}</td>
<td>
                        <span class="badge {{ $registrationBadgeClass }}">
                            {{ $registrationStatusText }}
                        </span>
                    </td>
                    <td><div class="d-flex gap-2">
                        <form method="POST" action="{{ route('admin.registrations.status', $registration) }}">@csrf @method('PATCH')
                            <input type="hidden" name="registration_status" value="approved"><button class="btn btn-sm btn-success">Setujui</button>
                        </form>
                        <form method="POST" action="{{ route('admin.registrations.status', $registration) }}">@csrf @method('PATCH')
                            <input type="hidden" name="registration_status" value="rejected"><button class="btn btn-sm btn-outline-danger">Tolak</button>
                        </form>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-secondary py-4">Belum ada pendaftaran donor.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($registrations->hasPages())<div class="card-footer bg-white">{{ $registrations->links() }}</div>@endif
</div>
@endsection
