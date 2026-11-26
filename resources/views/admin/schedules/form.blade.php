@extends('layouts.app')

@php
    $isEditing = $schedule->exists;
    $formTitle = 'Tambah Jadwal';
    $headingText = 'Tambah Jadwal Donor';
    $submitText = 'Simpan Jadwal';
    $formAction = route('admin.schedules.store');

    if ($isEditing) {
        $formTitle = 'Edit Jadwal';
        $headingText = 'Edit Jadwal Donor';
        $submitText = 'Simpan Perubahan';
        $formAction = route('admin.schedules.update', $schedule);
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
                    @if ($schedule->exists) @method('PUT') @endif
                    <div class="mb-3"><label class="form-label" for="title">Nama Jadwal</label><input id="title" name="title" class="form-control" value="{{ old('title', $schedule->title) }}" required></div>
                    <div class="mb-3"><label class="form-label" for="location">Lokasi</label><input id="location" name="location" class="form-control" value="{{ old('location', $schedule->location) }}" required></div>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="schedule_date">Tanggal</label><input id="schedule_date" type="date" name="schedule_date" class="form-control" value="{{ old('schedule_date', $schedule->schedule_date?->format('Y-m-d')) }}" required></div>
                        <div class="col-md-4"><label class="form-label" for="start_time">Jam Mulai</label><input id="start_time" type="time" name="start_time" class="form-control" value="{{ old('start_time', substr($schedule->start_time ?? '',0,5)) }}" required></div>
                        <div class="col-md-4"><label class="form-label" for="end_time">Jam Selesai</label><input id="end_time" type="time" name="end_time" class="form-control" value="{{ old('end_time', substr($schedule->end_time ?? '',0,5)) }}" required></div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6"><label class="form-label" for="quota">Kuota Pendonor</label><input id="quota" type="number" min="1" name="quota" class="form-control" value="{{ old('quota', $schedule->quota ?? 50) }}" required></div>
                        <div class="col-md-6"><label class="form-label" for="status">Status</label><select id="status" name="status" class="form-select" required><option value="open" @selected(old('status', $schedule->status ?? 'open') === 'open')>Dibuka</option><option value="closed" @selected(old('status', $schedule->status ?? 'open') === 'closed')>Ditutup</option></select></div>
                    </div>
                    <div class="mt-4 d-flex flex-wrap gap-3"><button class="btn btn-danger px-4">{{ $submitText }}</button><a href="{{ route('admin.schedules.index') }}" class="btn btn-outline-secondary px-4">Batal</a></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
