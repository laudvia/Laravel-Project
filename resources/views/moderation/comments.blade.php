@extends('layout')

@section('content')
    <div class="mt-4 mb-3 d-flex align-items-center justify-content-between">
        <h1 class="h4 mb-0">Модерация комментариев</h1>
        <div class="text-muted">Показаны только комментарии, которые ожидают проверки</div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($comments->count() === 0)
        <div class="alert alert-info">Новых комментариев для модерации нет.</div>
    @else
        <div class="list-group">
            @foreach($comments as $comment)
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start" style="gap: 1rem;">
                        <div class="flex-grow-1">
                            <div class="d-flex flex-wrap align-items-center" style="gap: .5rem;">
                                <strong>{{ $comment->author?->name ?? $comment->author_name }}</strong>
                                @if($comment->author_email)
                                    <span class="text-muted">({{ $comment->author_email }})</span>
                                @endif
                                <span class="text-muted">•</span>
                                <small class="text-muted">{{ $comment->created_at->format('d.m.Y H:i') }}</small>
                            </div>

                            <div class="mt-1">
                                <span class="text-muted">Статья:</span>
                                <a href="{{ route('articles.show', $comment->article) }}">{{ $comment->article?->title ?? '—' }}</a>
                            </div>

                            <div class="mt-2" style="white-space: pre-wrap;">{{ $comment->body }}</div>
                        </div>

                        <div class="d-flex flex-column" style="gap: .5rem; min-width: 160px;">
                            <form method="POST" action="{{ route('moderator.comments.approve', $comment) }}" class="m-0">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-sm w-100">Принять</button>
                            </form>

                            <form method="POST" action="{{ route('moderator.comments.reject', $comment) }}" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Отклонить и удалить комментарий?')">Отклонить</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3">
            {{ $comments->links() }}
        </div>
    @endif
@endsection
