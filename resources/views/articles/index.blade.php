@extends('layout')

@section('content')
    <div class="d-flex align-items-center justify-content-between mt-4 mb-3">
        <h1 class="h3 mb-0">Новости</h1>
        <div>
            <a class="btn btn-primary btn-sm" href="{{ route('articles.create') }}">Создать</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($articles->count() === 0)
        <div class="alert alert-info">
            Новостей пока нет. Запустите миграции и сидер, чтобы наполнить таблицу тестовыми данными.
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
                        <p class="mb-1">{{ $article->excerpt }}</p>
                    @endif

                    <div class="d-flex align-items-center mt-2">
                        <a class="btn btn-sm btn-outline-secondary mr-2" href="{{ route('articles.edit', $article) }}">Редактировать</a>
                        <a class="btn btn-sm btn-outline-primary mr-2" href="{{ route('articles.comments.index', $article) }}">Комментарии</a>

                        <form method="POST" action="{{ route('articles.destroy', $article) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить статью? Комментарии удалятся тоже.')">
                                Удалить
                            </button>
                        </form>
                    </div>

                    <small class="text-muted d-block mt-2">ID: {{ $article->id }}</small>
                </div>
            @endforeach
        </div>

        <div class="mt-3">
            {{ $articles->links() }}
        </div>
    @endif
@endsection
