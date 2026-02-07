@extends('layout')

@section('content')
<div class="container py-3" style="max-width: 900px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Комментарии к: <a href="{{ route('articles.show', $article) }}">{{ $article->title }}</a></h1>
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('articles.show', $article) }}">Назад к статье</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">
      @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
  @endif

  @auth
    <form method="POST" action="{{ route('articles.comments.store', $article) }}" class="mb-4">
      @csrf
      <div class="mb-2">
        <label class="form-label">Новый комментарий</label>
        <textarea class="form-control" name="text" rows="3" required>{{ old('text') }}</textarea>
      </div>
      <button class="btn btn-primary">Отправить</button>
      <div class="text-muted small mt-2">Если включена модерация, комментарий появится после одобрения.</div>
    </form>
  @else
    <div class="alert alert-info">
      <a href="{{ route('login') }}">Войдите</a>, чтобы оставить комментарий.
    </div>
  @endauth

  <div class="list-group">
    @forelse($comments as $comment)
      <div class="list-group-item">
        <div class="d-flex justify-content-between">
          <strong>{{ $comment->author?->name ?? ($comment->author_name ?? 'User') }}</strong>
          <small class="text-muted">{{ optional($comment->created_at)->format('d.m.Y H:i') }}</small>
        </div>
        <div class="mt-2" style="white-space: pre-wrap;">
          {{ $comment->content ?? $comment->body ?? $comment->text ?? '' }}
        </div>
        @if(isset($comment->is_approved) && $comment->is_approved === false)
          <div class="mt-2">
            <span class="badge bg-warning text-dark">На модерации</span>
          </div>
        @endif
      </div>
    @empty
      <div class="list-group-item text-muted">Комментариев пока нет.</div>
    @endforelse
  </div>
</div>
@endsection
