@extends('layout')

@section('content')
    <div class="d-flex align-items-center justify-content-between mt-4 mb-3">
        <h1 class="h3 mb-0">Новости</h1>
        <span class="text-muted">Всего: {{ $articles->total() }}</span>
    </div>

    @if($articles->count() === 0)
        <div class="alert alert-info">
            Новостей пока нет. Запустите миграции и сидер, чтобы наполнить таблицу тестовыми данными.
        </div>
    @else
        <div class="list-group">
            @foreach($articles as $article)
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">{{ $article->title }}</h5>
                        <small class="text-muted">
                            {{ $article->published_at ? $article->published_at->format('d.m.Y H:i') : '—' }}
                        </small>
                    </div>

                    @if(!empty($article->excerpt))
                        <p class="mb-1">{{ $article->excerpt }}</p>
                    @endif

                    <small class="text-muted">ID: {{ $article->id }}</small>
                </div>
            @endforeach
        </div>

        <div class="mt-3">
            {{ $articles->links() }}
        </div>
    @endif
@endsection
