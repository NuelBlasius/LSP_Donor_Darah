@extends('layouts.app')

@php
    $isEditing = $announcement->exists;
    $formTitle = 'Buat Pengumuman';
    $headingText = 'Buat Pengumuman';
    $submitText = 'Publikasikan';
    $formAction = route('admin.announcements.store');

    if ($isEditing) {
        $formTitle = 'Edit Pengumuman';
        $headingText = 'Edit Pengumuman';
        $submitText = 'Simpan Perubahan';
        $formAction = route('admin.announcements.update', $announcement);
    }
@endphp

@section('title', $formTitle)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3"><h1 class="h4 fw-bold mb-0">{{ $headingText }}</h1></div>
            <div class="card-body p-4">
                @if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                <form method="POST" action="{{ $formAction }}">
                    @csrf
                    @if ($announcement->exists) @method('PUT') @endif
                    <div class="mb-3"><label for="title" class="form-label">Judul</label><input id="title" name="title" class="form-control" value="{{ old('title', $announcement->title) }}" required></div>
                    <div class="mb-3"><label for="content" class="form-label">Isi Pengumuman</label><textarea id="content" name="content" rows="6" class="form-control" required>{{ old('content', $announcement->content) }}</textarea></div>
                    <div class="d-flex flex-wrap gap-3 mt-4"><button class="btn btn-danger px-4">{{ $submitText }}</button>
                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary px-4">Batal</a></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
