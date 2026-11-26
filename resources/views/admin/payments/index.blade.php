@extends('layouts.app')

@section('title', 'Verifikasi Pembayaran | DonorDarah')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h1 class="h2 fw-bold">Verifikasi Pembayaran</h1><p class="text-secondary mb-0">Periksa bukti pembayaran yang dikirim pendonor.</p></div>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Pendonor</th><th>Jadwal</th><th>Bukti</th><th>Status</th><th>Tindakan</th></tr></thead>
            <tbody>
            @forelse ($payments as $payment)
                @php
                    $paymentBadgeClass = 'text-bg-warning';
                    $paymentStatusText = 'Menunggu';

                    if ($payment->payment_status === 'approved') {
                        $paymentBadgeClass = 'text-bg-success';
                        $paymentStatusText = 'Disetujui';
                    } elseif ($payment->payment_status === 'rejected') {
                        $paymentBadgeClass = 'text-bg-danger';
                        $paymentStatusText = 'Ditolak';
                    }
                @endphp
                <tr>
                    <td>{{ $payment->user?->name }}<div class="small text-secondary">{{ $payment->user?->email }}</div></td>
                    <td>{{ $payment->schedule?->title }}</td>
                    <td><a href="{{ asset('storage/'.$payment->payment_proof) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">Lihat Bukti</a></td>
<td>
                        <span class="badge {{ $paymentBadgeClass }}">
                            {{ $paymentStatusText }}
                        </span>
                    </td>
                    <td><div class="d-flex gap-2">
                        <form method="POST" action="{{ route('admin.payments.status', $payment) }}">@csrf @method('PATCH')
                            <input type="hidden" name="payment_status" value="approved"><button class="btn btn-sm btn-success">Setujui</button>
                        </form>
                        <form method="POST" action="{{ route('admin.payments.status', $payment) }}">@csrf @method('PATCH')
                            <input type="hidden" name="payment_status" value="rejected"><button class="btn btn-sm btn-outline-danger">Tolak</button>
                        </form>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-secondary py-4">Belum ada bukti pembayaran yang dikirim.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($payments->hasPages())<div class="card-footer bg-white">{{ $payments->links() }}</div>@endif
</div>
@endsection
