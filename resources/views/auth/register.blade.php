@extends('layout')

@section('content')
<div class="container py-4" style="max-width:520px">
  <h1 class="h3 mb-3">Регистрация</h1>

  @if ($errors->any())
    <div class="alert alert-danger">
      @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
  @endif

  <form method="POST" action="{{ route('register.perform') }}">
    @csrf

    <div class="mb-3">
      <label class="form-label">Имя</label>
      <input class="form-control" type="text" name="name" required value="{{ old('name', 'Test User') }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input class="form-control" type="email" name="email" required value="{{ old('email', 'test_web@example.com') }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Пароль</label>
      <input class="form-control" type="password" name="password" required value="password123">
    </div>

    <div class="mb-3">
      <label class="form-label">Повтор пароля</label>
      <input class="form-control" type="password" name="password_confirmation" required value="password123">
    </div>

    <button class="btn btn-primary">Зарегистрироваться</button>
    <a class="btn btn-outline-secondary ms-2" href="{{ route('login') }}">Вход</a>
  </form>

  <div class="text-muted small mt-3">
    Эта форма делает WEB-регистрацию (cookie-сессия). API-токен остаётся отдельно: <code>POST /api/register</code>.
  </div>
</div>
@endsection
