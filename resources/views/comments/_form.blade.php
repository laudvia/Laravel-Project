<div class="form-group">
    <label>Автор</label>

    @php
        $authorName = $comment->exists
            ? ($comment->author?->name ?? $comment->author_name)
            : (auth()->user()?->name ?? '');

        $authorEmail = $comment->exists
            ? ($comment->author_email ?? '')
            : (auth()->user()?->email ?? '');
    @endphp

    <input
        type="text"
        class="form-control"
        value="{{ trim($authorName . ($authorEmail ? ' (' . $authorEmail . ')' : '')) }}"
        readonly
    >
</div>

<div class="form-group">
    <label for="body">Комментарий</label>
    <textarea
        id="body"
        name="body"
        rows="4"
        class="form-control @error('body') is-invalid @enderror"
        required
    >{{ old('body', $comment->body) }}</textarea>
    @error('body')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="alert alert-info">
    Редактировать и удалять комментарии может только автор комментария и модератор.
</div>
