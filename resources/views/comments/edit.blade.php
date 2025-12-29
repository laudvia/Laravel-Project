@extends('layout')

@section('content')
    <div class="mt-4 mb-3 d-flex align-items-center justify-content-between">
        <h1 class="h4 mb-0">Редактировать комментарий</h1>
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('articles.show', $article) }}">Назад</a>
    </div>

    <form method="POST" action="{{ route('comments.update', $comment) }}">
        @csrf
        @method('PUT')

        @include('comments._form', ['article' => $article, 'comment' => $comment])

        <button type="submit" class="btn btn-primary">Обновить</button>
    </form>
@endsection
