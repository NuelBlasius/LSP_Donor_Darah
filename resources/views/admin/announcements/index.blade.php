@extends('layouts.app')

@section('title', 'Kelola Pengumuman | DonorDarah')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
    <div><h1 class="h2 fw-bold">Kelola Pengumuman</h1><p class="text-secondary mb-0">Informasi ini akan tampil pada dashboard pendonor.</p></div>
    <a href="{{ route('admin.announcements.create') }}" class="btn btn-danger btn-lg d-inline-flex align-items-center justify-content-center flex-shrink-0 px-4"><span class="me-2" aria-hidden="true">＋</span>Buat Pengumuman</a>
</div>
<div class="row g-3">
    @forelse ($announcements as $announcement)
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="small text-danger fw-semibold">{{ $announcement->created_at->format('d M Y') }}</div>
                    <h2 class="h5 fw-bold mt-2">{{ $announcement->title }}</h2>
                    <p class="text-secondary">{{ $announcement->content }}</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" onsubmit="return confirm('Hapus pengumuman ini?');">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="alert alert-info">Belum ada pengumuman. Buat pengumuman pertama.</div></div>
    @endforelse
</div>
@if ($announcements->hasPages())<div class="mt-3">{{ $announcements->links() }}</div>@endif
@endsection
