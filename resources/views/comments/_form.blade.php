<div class="form-group">
    <label for="author_name">Имя</label>
    <input
        type="text"
        id="author_name"
        name="author_name"
        class="form-control @error('author_name') is-invalid @enderror"
        value="{{ old('author_name', $comment->author_name) }}"
        required
    >
    @error('author_name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="author_email">Email (необязательно)</label>
    <input
        type="email"
        id="author_email"
        name="author_email"
        class="form-control @error('author_email') is-invalid @enderror"
        value="{{ old('author_email', $comment->author_email) }}"
    >
    @error('author_email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
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
