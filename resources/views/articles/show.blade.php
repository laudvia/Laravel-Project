@extends('layout')

@section('content')
    <div class="mt-4 mb-3 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="h3 mb-0">{{ $article->title }}</h1>
            <div class="text-muted">
                {{ $article->published_at ? $article->published_at->format('d.m.Y H:i') : 'Дата не указана' }}
            </div>
        </div>
        <div class="d-flex align-items-center flex-wrap" style="gap: .5rem;">
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('articles.index') }}">К списку</a>

            @can('update', $article)
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('articles.edit', $article) }}">Редактировать</a>
            @endcan

            @can('delete', $article)
                <form method="POST" action="{{ route('articles.destroy', $article) }}" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Удалить статью? Комментарии удалятся тоже.')">Удалить</button>
                </form>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(!empty($article->excerpt))
        <p class="lead">{{ $article->excerpt }}</p>
    @endif

    <div class="mb-4" style="white-space: pre-wrap;">{{ $article->content }}</div>

    <hr class="my-4">

<div class="d-flex align-items-center justify-content-between mb-2">
    <h2 class="h5 mb-0">Комментарии</h2>

    <div class="d-flex align-items-center" style="gap:.5rem;">
        @auth
            @if(\Illuminate\Support\Facades\Route::has('articles.comments.index'))
                <a class="btn btn-outline-primary btn-sm" href="{{ route('articles.comments.index', $article) }}">Все комментарии</a>
            @endif
        @else
            <a class="btn btn-outline-primary btn-sm" href="{{ route('login') }}">Войти</a>
        @endauth
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if($latestComments->count() === 0)
    <div class="alert alert-info">Комментариев пока нет.</div>
@else
    <div class="list-group mb-3">
        @foreach($latestComments as $comment)
            <div class="list-group-item">
                <div class="d-flex justify-content-between">
                    <strong>{{ $comment->author?->name ?? $comment->author_name }}</strong>
                    <small class="text-muted">{{ $comment->created_at->format('d.m.Y H:i') }}</small>
                </div>

                <div class="mt-2" style="white-space: pre-wrap;">{{ $comment->body }}</div>

                @auth
                    @php($canManage = \Illuminate\Support\Facades\Gate::allows('is-moderator') || ($comment->user_id && $comment->user_id === auth()->id()))
                    @if($canManage)
                        <div class="mt-2 d-flex align-items-center flex-wrap" style="gap:.5rem;">
                            @if(\Illuminate\Support\Facades\Route::has('comments.edit'))
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('comments.edit', $comment) }}">Редактировать</a>
                            @endif

                            @if(\Illuminate\Support\Facades\Route::has('comments.destroy'))
                                <form method="POST" action="{{ route('comments.destroy', $comment) }}" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Удалить комментарий?')">Удалить</button>
                                </form>
                            @endif
                        </div>
                    @endif
                @endauth
            </div>
        @endforeach
    </div>
@endif

<div class="card">
    <div class="card-body">
        <h3 class="h6">Добавить комментарий</h3>

        @auth
            @if(\Illuminate\Support\Facades\Route::has('articles.comments.store'))
                <form method="POST" action="{{ route('articles.comments.store', $article) }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Автор</label>
                        <input type="text" class="form-control"
                               value="{{ auth()->user()->name }} ({{ auth()->user()->email }})" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="body">Комментарий</label>
                        <textarea id="body" name="body" rows="4"
                                  class="form-control @error('body') is-invalid @enderror"
                                  required>{{ old('body') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Отправить</button>
                </form>
            @else
                <div class="alert alert-warning mb-0">Роут для отправки комментария не найден (articles.comments.store).</div>
            @endif
        @else
            <div class="alert alert-warning mb-0">
                Чтобы добавить комментарий, нужно <a href="{{ route('login') }}">войти</a>.
            </div>
        @endauth
    </div>
</div>

@endsection
