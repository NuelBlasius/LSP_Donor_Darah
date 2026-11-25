
@extends('layouts.app')

@section('title', 'Login - Donor Darah')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">

        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0">Login Pendonor</h4>
            </div>

            <div class="card-body">

                <p class="text-muted">
                    Masuk ke akun Donor Darah Anda.
                </p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control"
                            value="{{ old('email') }}"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Password
                        </label>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-danger w-100">
                        Login
                    </button>
                </form>

                <div class="text-center mt-3">
                    Belum memiliki akun?
                    <a href="{{ route('register') }}">Daftar</a>
                </div>

            </div>
        </div>

    </div>
</div>

@endsection
