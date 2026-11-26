@extends('layouts.app')

@section('title', 'Riwayat Pendaftaran | DonorDarah')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h2 fw-bold">Riwayat Pendaftaran</h1>
        <p class="text-secondary mb-0">Lihat status pendaftaran jadwal donor dan pembayaran.</p>
    </div>
    <a href="{{ route('services') }}" class="btn btn-danger align-self-start">
        Cari Jadwal Donor
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Jadwal</th>
                    <th>Tanggal</th>
                    <th>Status Pendaftaran</th>
                    <th>Status Pembayaran</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($registrations as $registration)
                    @php
                        $registrationBadgeClass = 'text-bg-warning';
                        $registrationStatusText = 'Menunggu';
                        $paymentBadgeClass = 'text-bg-secondary';
                        $paymentStatusText = 'Menunggu';

                        if ($registration->registration_status === 'approved') {
                            $registrationBadgeClass = 'text-bg-success';
                            $registrationStatusText = 'Disetujui';
                        } elseif ($registration->registration_status === 'rejected') {
                            $registrationBadgeClass = 'text-bg-danger';
                            $registrationStatusText = 'Ditolak';
                        }

                        if ($registration->payment_status === 'approved') {
                            $paymentBadgeClass = 'text-bg-success';
                            $paymentStatusText = 'Disetujui';
                        } elseif ($registration->payment_status === 'rejected') {
                            $paymentBadgeClass = 'text-bg-danger';
                            $paymentStatusText = 'Ditolak';
                        }
                    @endphp
                    <tr>
                        <td>
                            {{ $registration->schedule?->title ?? 'Jadwal dihapus' }}
                            <div class="small text-secondary">
                                {{ $registration->schedule?->location }}
                            </div>
                        </td>
                        <td>
                            {{ $registration->schedule?->schedule_date?->format('d M Y') ?? '-' }}
                        </td>
                        <td>
                            <span class="badge {{ $registrationBadgeClass }}">
                                {{ $registrationStatusText }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $paymentBadgeClass }}">
                                {{ $paymentStatusText }}
                            </span>
                        </td>
                        <td>
                            @if ($registration->registration_status === 'approved' && $registration->payment_status !== 'approved')
                                <a
                                    href="{{ route('donor.registrations.payment.form', $registration) }}"
                                    class="btn btn-sm btn-outline-danger"
                                >
                                    Konfirmasi Pembayaran
                                </a>
                            @elseif ($registration->registration_status === 'pending')
                                <span class="small text-secondary">Menunggu admin</span>
                            @elseif ($registration->registration_status === 'rejected')
                                <span class="small text-secondary">Pendaftaran ditolak</span>
                            @else
                                <span class="small text-success">Pembayaran disetujui</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-secondary py-4">
                            Belum ada riwayat pendaftaran.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($registrations->hasPages())
        <div class="card-footer bg-white">
            {{ $registrations->links() }}
        </div>
    @endif
</div>
@endsection
