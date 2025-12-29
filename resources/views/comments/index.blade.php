@extends('layout')

@section('content')
    <div class="mt-4 mb-3 d-flex align-items-center justify-content-between">
        <h1 class="h4 mb-0">Комментарии к статье: {{ $article->title }}</h1>
        <div>
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('articles.show', $article) }}">К статье</a>
            <a class="btn btn-primary btn-sm" href="{{ route('articles.comments.create', $article) }}">Добавить</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($comments->count() === 0)
        <div class="alert alert-info">Комментариев пока нет.</div>
    @else
        <div class="list-group">
            @foreach($comments as $comment)
                <div class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <strong>{{ $comment->author_name }}</strong>
                        <small class="text-muted">{{ $comment->created_at->format('d.m.Y H:i') }}</small>
                    </div>

                    @if($comment->author_email)
                        <div class="text-muted">{{ $comment->author_email }}</div>
                    @endif

                    <div class="mt-2" style="white-space: pre-wrap;">{{ $comment->body }}</div>

                    <div class="mt-2 d-flex">
                        <a class="btn btn-sm btn-outline-secondary mr-2" href="{{ route('comments.edit', $comment) }}">Редактировать</a>

                        <form method="POST" action="{{ route('comments.destroy', $comment) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Удалить комментарий?')">
                                Удалить
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3">
            {{ $comments->links() }}
        </div>
    @endif
@endsection
