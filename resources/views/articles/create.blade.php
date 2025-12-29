@extends('layout')

@section('content')
    <div class="mt-4 mb-3 d-flex align-items-center justify-content-between">
        <h1 class="h4 mb-0">Создать статью</h1>
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('articles.index') }}">Назад</a>
    </div>

    <form method="POST" action="{{ route('articles.store') }}">
        @csrf
        @include('articles._form', ['article' => $article])

        <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>
@endsection
