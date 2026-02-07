@extends('layout')

@section('content')
<div class="container py-4" style="max-width: 820px;">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="h4 mb-0">Редактировать комментарий</h1>
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('articles.show', $comment->article_id) }}">Назад к статье</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('comments.update', $comment) }}">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label class="form-label" for="body">Текст</label>
            <textarea id="body" name="body" rows="5"
                      class="form-control @error('body') is-invalid @enderror"
                      required>{{ old('body', $comment->body) }}</textarea>
            @error('body')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-primary">Сохранить</button>

        <div class="text-muted small mt-3">
            Если комментарий редактирует обычный пользователь — он снова уйдёт на модерацию.
        </div>
    </form>
</div>
@endsection
