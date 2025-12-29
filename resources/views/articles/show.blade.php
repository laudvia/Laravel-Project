@extends('layout')

@section('content')
    <div class="d-flex align-items-center justify-content-between mt-4 mb-3">
        <h1 class="h3 mb-0">{{ $article->title }}</h1>
        <div class="d-flex" style="gap: .5rem;">
            <a class="btn btn-outline-secondary" href="{{ route('articles.index') }}">К списку</a>
            <a class="btn btn-outline-primary" href="{{ route('articles.edit', $article) }}">Редактировать</a>
            <form method="POST" action="{{ route('articles.destroy', $article) }}" onsubmit="return confirm('Удалить новость?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">Удалить</button>
            </form>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <div class="text-muted mb-2">
                <strong>Опубликовано:</strong>
                {{ $article->published_at ? $article->published_at->format('d.m.Y H:i') : '—' }}
            </div>

            @if(!empty($article->excerpt))
                <p class="lead">{{ $article->excerpt }}</p>
                <hr>
            @endif

            <div style="white-space: pre-wrap;">{{ $article->content }}</div>
        </div>
    </div>
@endsection
