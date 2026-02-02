<!doctype html>
<html lang="en">
<head>
    <title>News</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Vite (Vue + Laravel Echo) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

                    @can('is-moderator')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('moderator.comments.index') }}">Модерация комментариев</a>
                        </li>
                    @endcan
                </ul>

                {{-- Правая часть навбара --}}
                <ul class="navbar-nav align-items-center">
                    @auth
                        @php($unreadNotifications = auth()->user()->unreadNotifications)

                        <li class="nav-item dropdown d-flex align-items-center me-2">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                               aria-expanded="false">
                                Уведомления
                                @if($unreadNotifications->count() > 0)
                                    <span class="badge bg-danger">{{ $unreadNotifications->count() }}</span>
                                @endif
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 320px;">
                                @forelse($unreadNotifications as $notification)
                                    <li>
                                        <a class="dropdown-item text-wrap"
                                           href="{{ route('notifications.open', $notification->id) }}">
                                            {{ $notification->data['title'] ?? 'Новая статья' }}
                                        </a>
                                    </li>
                                @empty
                                    <li>
                                        <span class="dropdown-item-text text-muted">Нет непрочитанных уведомлений</span>
                                    </li>
                                @endforelse
                            </ul>
                        </li>

                        {{-- Имя пользователя (как nav-link, чтобы совпадали высоты/отступы) --}}
                        <li class="nav-item d-flex align-items-center">
                            <span class="nav-link me-3 py-0">{{ auth()->user()->name }}</span>
                        </li>

                        <li class="nav-item d-flex align-items-center">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline m-0">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item d-flex align-items-center">
                            <a class="nav-link" href="{{ route('register.form') }}">Sign Up</a>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <a class="nav-link" href="{{ route('login') }}">Sign In</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
</header>

<!-- Vue-компонент для пуш-уведомлений о новых статьях -->
<div id="app"></div>

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
