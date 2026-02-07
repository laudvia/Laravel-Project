@extends('layout')

@section('content')
<div class="container py-4" style="max-width:520px">
  <h1 class="h3 mb-3">Вход</h1>

  @if ($errors->any())
    <div class="alert alert-danger">
      @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
  @endif

  <form method="POST" action="{{ route('login.perform') }}">
    @csrf
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input class="form-control" type="email" name="email" required value="{{ old('email', 'moderator@example.com') }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Пароль</label>
      <input class="form-control" type="password" name="password" required value="password">
    </div>

    <button class="btn btn-primary">Войти</button>
    <a class="btn btn-outline-secondary ms-2" href="{{ route('register.form') }}">Регистрация</a>
  </form>

  <div class="text-muted small mt-3">
    Эта форма делает WEB-вход (cookie-сессия). API-токен остаётся отдельно: <code>POST /api/login</code>.
  </div>
</div>
@endsection
