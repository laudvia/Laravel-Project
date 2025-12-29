@extends('layout')

@section('content')
    <div class="d-flex align-items-center justify-content-between mt-4 mb-3">
        <h1 class="h3 mb-0">Новости</h1>
        <a class="btn btn-primary" href="{{ route('articles.create') }}">Добавить новость</a>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if($articles->count() === 0)
        <div class="alert alert-info">
            Новостей пока нет. Создайте первую новость.
        </div>
    @else
        <div class="list-group">
            @foreach($articles as $article)
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">
                            <a href="{{ route('articles.show', $article) }}">{{ $article->title }}</a>
                        </h5>
                        <small class="text-muted">
                            {{ $article->published_at ? $article->published_at->format('d.m.Y H:i') : '—' }}
                        </small>
                    </div>

                    @if(!empty($article->excerpt))
                        <p class="mb-2">{{ $article->excerpt }}</p>
                    @endif

                    <div class="d-flex" style="gap: .5rem;">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('articles.edit', $article) }}">Редактировать</a>

                        <form method="POST" action="{{ route('articles.destroy', $article) }}" onsubmit="return confirm('Удалить новость?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Удалить</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3">
            {{ $articles->links() }}
        </div>
    @endif
@endsection
