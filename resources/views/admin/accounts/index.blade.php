@extends('layouts.app')

@section('title', 'Verifikasi Akun | DonorDarah')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h1 class="h2 fw-bold">Verifikasi Akun Pendonor</h1><p class="text-secondary mb-0">Setujui atau tolak akun yang telah mendaftar.</p></div>
</div>
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Nama</th><th>NIK</th><th>Telepon</th><th>Email</th><th>Status</th><th>Tindakan</th></tr></thead>
            <tbody>
            @forelse ($users as $user)
                @php
                    $accountBadgeClass = 'text-bg-warning';
                    $accountStatusText = 'Menunggu';

                    if ($user->account_status === 'approved') {
                        $accountBadgeClass = 'text-bg-success';
                        $accountStatusText = 'Disetujui';
                    } elseif ($user->account_status === 'rejected') {
                        $accountBadgeClass = 'text-bg-danger';
                        $accountStatusText = 'Ditolak';
                    }
                @endphp
                <tr>
                    <td class="fw-semibold">{{ $user->name }}</td><td>{{ $user->nik ?? '-' }}</td><td>{{ $user->nomor_telepon ?? '-' }}</td><td>{{ $user->email }}</td>
<td>
                        <span class="badge {{ $accountBadgeClass }}">
                            {{ $accountStatusText }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <form method="POST" action="{{ route('admin.accounts.status', $user) }}">@csrf @method('PATCH')
                                <input type="hidden" name="account_status" value="approved"><button class="btn btn-sm btn-success" type="submit">Setujui</button>
                            </form>
                            <form method="POST" action="{{ route('admin.accounts.status', $user) }}">@csrf @method('PATCH')
                                <input type="hidden" name="account_status" value="rejected"><button class="btn btn-sm btn-outline-danger" type="submit">Tolak</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-secondary py-4">Belum ada akun pendonor.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($users->hasPages())<div class="card-footer bg-white">{{ $users->links() }}</div>@endif
</div>
@endsection
