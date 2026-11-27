@extends('layouts.app')

@section('title', 'Kelola Jadwal | DonorDarah')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
    <div><h1 class="h2 fw-bold">Kelola Jadwal Donor</h1><p class="text-secondary mb-0">Tambah, ubah, tutup, atau hapus jadwal donor.</p></div>
    <a href="{{ route('admin.schedules.create') }}" class="btn btn-danger btn-lg d-inline-flex align-items-center justify-content-center flex-shrink-0 px-4"><span class="me-2" aria-hidden="true">＋</span>Tambah Jadwal</a>
</div>

<form method="GET" action="{{ route('admin.schedules.index') }}" class="card border-0 shadow-sm p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-6">
            <label for="search" class="form-label">Nama jadwal</label>
            <input id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari nama jadwal donor">
        </div>
        <div class="col-md-4">
            <label for="date" class="form-label">Tanggal donor</label>
            <input id="date" type="date" name="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-danger flex-grow-1" type="submit">Cari</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.schedules.index') }}">Reset</a>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Nama Jadwal</th><th>Tanggal</th><th>Lokasi</th><th>Kuota</th><th>Nominal</th><th>Status</th><th>Tindakan</th></tr></thead>
            <tbody>
            @forelse ($schedules as $schedule)
                @php
                    $scheduleBadgeClass = 'text-bg-secondary';
                    $scheduleStatusText = 'Ditutup';

                    if ($schedule->status === 'open') {
                        $scheduleBadgeClass = 'text-bg-success';
                        $scheduleStatusText = 'Dibuka';
                    }
                @endphp
                <tr>
                    <td class="fw-semibold">{{ $schedule->title }}</td><td>{{ $schedule->schedule_date->format('d M Y') }}<div class="small text-secondary">{{ substr($schedule->start_time,0,5) }}–{{ substr($schedule->end_time,0,5) }}</div></td>
                    <td>
                        @if ($schedule->map_url)
                            <a class="map-location" href="{{ $schedule->map_url }}" target="_blank" rel="noopener noreferrer">{{ $schedule->map_address }}</a>
                        @else
                            {{ $schedule->map_address }}
                        @endif
                    </td>
                    <td>{{ $schedule->quota }}</td><td>Rp {{ number_format((int) $schedule->amount, 0, ',', '.') }}</td>
<td>
                        <span class="badge {{ $scheduleBadgeClass }}">
                            {{ $scheduleStatusText }}
                        </span>
                    </td>
                    <td><div class="d-flex gap-2">
                        <a href="{{ route('admin.schedules.edit', $schedule) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}" onsubmit="return confirm('Hapus jadwal ini? Pendaftaran terkait juga akan terhapus.');">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-secondary py-4">Belum ada jadwal. Klik Tambah Jadwal untuk membuat jadwal donor.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($schedules->hasPages())<div class="card-footer bg-white">{{ $schedules->links() }}</div>@endif
</div>
@endsection
