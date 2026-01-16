<!doctype html>
<html lang="en">
<head>
    <title>News</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">Navbar</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/about') }}">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/contact') }}">Contacts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/articles') }}">News (DB)</a>
                    </li>

                    @can('create', \App\Models\Article::class)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('articles.create') }}">Создать новость</a>
                        </li>
                    @endcan
                </ul>

                <ul class="navbar-nav mb-2 mb-lg-0">
                    @auth
                        <li class="nav-item">
                            <span class="navbar-text me-3">{{ auth()->user()->name }}</span>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register.form') }}">Sign Up</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Sign In</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
</header>

<main>
    <div class="container py-3">
        @yield('content')
    </div>
</main>

<footer>
    <div class="container py-3">
        @yield('name')
    </div>
</footer>

<!-- Bootstrap 5 JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
