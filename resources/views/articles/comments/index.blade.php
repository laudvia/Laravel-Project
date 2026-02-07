@extends('layout')

@section('content')
<div class="container py-3" style="max-width:900px">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 m-0">Комментарии к статье: <a href="{{ route('articles.show', $article) }}">{{ $article->title }}</a></h1>
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('articles.show', $article) }}">Назад к статье</a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
  @endif

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @auth
    <form method="POST" action="{{ route('articles.comments.store', $article) }}" class="card card-body mb-3">
      @csrf
      <label class="form-label">Добавить комментарий</label>
      <textarea class="form-control mb-2" name="body" rows="3" required>{{ old('body') }}</textarea>
      <button class="btn btn-primary">Отправить</button>
      <div class="text-muted small mt-2">Модератор — сразу публикуется, остальные — на проверку.</div>
    </form>
  @else
    <div class="alert alert-info">Чтобы написать комментарий — <a href="{{ route('login') }}">войдите</a>.</div>
  @endauth

  <div class="list-group">
    @forelse($comments as $c)
      <div class="list-group-item">
        <div class="d-flex justify-content-between">
          <strong>{{ $c->author?->name ?? $c->author_name }}</strong>
          <small class="text-muted">{{ $c->created_at->format('d.m.Y H:i') }}</small>
        </div>
        <div class="mt-2" style="white-space: pre-wrap;">{{ $c->body }}</div>
        @if(!$c->is_approved)
          <div class="badge bg-warning text-dark mt-2">На модерации</div>
        @endif
      </div>
    @empty
      <div class="alert alert-info">Комментариев пока нет.</div>
    @endforelse
  </div>

  <div class="mt-3">{{ $comments->links() }}</div>
</div>
@endsection
