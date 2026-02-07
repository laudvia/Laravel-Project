@extends('layout')

@section('content')
<div class="container py-3" style="max-width: 900px;">
  <div class="d-flex align-items-center justify-content-between">
    <h1 class="h4 mb-3">Комментарии к: <a href="{{ route('articles.show', $article) }}">{{ $article->title }}</a></h1>
    <a class="btn btn-outline-secondary btn-sm" href="{{ route('articles.show', $article) }}">Назад</a>
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
        <textarea class="form-control" name="content" rows="3" placeholder="Ваш комментарий..." required>{{ old('content') }}</textarea>
      </div>
      <button class="btn btn-primary btn-sm">Отправить</button>
      <span class="text-muted ms-2 small">
        @can('is-moderator')
          (как модератор — сразу публикуется)
        @else
          (как пользователь — на проверку)
        @endcan
      </span>
    </form>
  @else
    <div class="alert alert-info">
      Чтобы комментировать, нужно <a href="{{ route('login') }}">войти</a>.
    </div>
  @endauth

  <div class="list-group">
    @forelse($comments as $c)
      <div class="list-group-item">
        <div class="d-flex justify-content-between">
          <strong>{{ $c->author?->name ?? ($c->author_name ?? 'User') }}</strong>
          <small class="text-muted">{{ $c->created_at?->format('d.m.Y H:i') }}</small>
        </div>

        <div class="mt-2" style="white-space: pre-wrap;">
          {{ $c->content ?? $c->body ?? $c->text ?? $c->message ?? $c->comment ?? $c->comment_text ?? '' }}
        </div>

        @php
          $pending = false;
          if (isset($c->is_approved)) $pending = !$c->is_approved;
          elseif (isset($c->approved)) $pending = !$c->approved;
          elseif (isset($c->status)) $pending = ($c->status === 'pending');
        @endphp

        @if($pending)
          <div class="mt-2">
            <span class="badge bg-warning text-dark">На модерации</span>
          </div>
        @endif
      </div>
    @empty
      <div class="alert alert-secondary">Комментариев пока нет.</div>
    @endforelse
  </div>

  <div class="mt-3">
    {{ $comments->links() }}
  </div>
</div>
@endsection
