@extends('layout')

@section('content')
<div class="container py-3" style="max-width: 1000px;">
  <h1 class="h4 mb-3">Модерация комментариев</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="list-group">
    @forelse($comments as $c)
      <div class="list-group-item">
        <div class="d-flex justify-content-between">
          <div>
            <strong>{{ $c->author?->name ?? ($c->author_name ?? 'User') }}</strong>
            <span class="text-muted ms-2">#{{ $c->id }}</span>
          </div>
          <small class="text-muted">{{ $c->created_at?->format('d.m.Y H:i') }}</small>
        </div>

        <div class="mt-2" style="white-space: pre-wrap;">
          {{ $c->content ?? $c->body ?? $c->text ?? $c->message ?? $c->comment ?? $c->comment_text ?? '' }}
        </div>

        <div class="mt-3 d-flex gap-2">
          <form method="POST" action="{{ route('moderator.comments.approve', $c) }}">
            @csrf
            @method('PATCH')
            <button class="btn btn-success btn-sm">Одобрить</button>
          </form>

          <form method="POST" action="{{ route('moderator.comments.reject', $c) }}">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm" onclick="return confirm('Отклонить и удалить комментарий?')">Отклонить</button>
          </form>
        </div>
      </div>
    @empty
      <div class="alert alert-secondary">Нет комментариев на модерации.</div>
    @endforelse
  </div>

  <div class="mt-3">
    {{ $comments->links() }}
  </div>
</div>
@endsection
