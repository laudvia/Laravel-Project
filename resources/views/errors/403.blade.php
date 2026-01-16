@extends('layout')

@section('content')
    <div class="mt-4">
        <h1 class="h3">403 — Доступ запрещён</h1>

        <div class="alert alert-danger mt-3">
            {{ $message ?? 'Недостаточно прав для выполнения действия.' }}
        </div>

        <a class="btn btn-outline-secondary" href="{{ url()->previous() }}">Назад</a>
        <a class="btn btn-primary" href="{{ route('articles.index') }}">К новостям</a>
    </div>
@endsection
