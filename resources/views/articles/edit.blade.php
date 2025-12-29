@extends('layout')

@section('content')
    <div class="d-flex align-items-center justify-content-between mt-4 mb-3">
        <h1 class="h3 mb-0">Редактировать новость</h1>
        <a class="btn btn-outline-secondary" href="{{ route('articles.show', $article) }}">Назад</a>
    </div>

    <form method="POST" action="{{ route('articles.update', $article) }}">
        @csrf
        @method('PUT')

        @include('articles._form', ['article' => $article])

        <div class="d-flex" style="gap: .5rem;">
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="{{ route('articles.show', $article) }}" class="btn btn-light">Отмена</a>
        </div>
    </form>
@endsection
