<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Donor Darah')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container">

            <a class="navbar-brand" href="{{ route('dashboard') }}">
                Donor Darah
            </a>

            <div class="ms-auto">

                @guest
                    <a href="{{ route('login') }}"
                    class="btn btn-outline-light me-2">
                        Login
                    </a>

                    <a href="{{ route('register') }}"
                    class="btn btn-light text-danger">
                        Register
                    </a>
                @endguest

                @auth
                    <a href="{{ route('dashboard') }}"
                    class="btn btn-outline-light me-2">
                        Dashboard
                    </a>

                    <form action="{{ route('logout') }}"
                        method="POST"
                        class="d-inline">
                        @csrf

                        <button type="submit"
                                class="btn btn-light text-danger">
                            Logout
                        </button>
                    </form>
                @endauth

            </div>

        </div>
    </nav>

    <main class="container mt-4">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>
