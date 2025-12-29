<div class="form-group">
    <label for="title">Заголовок</label>
    <input
        id="title"
        type="text"
        name="title"
        class="form-control @error('title') is-invalid @enderror"
        value="{{ old('title', $article->title ?? '') }}"
        required
    >
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="excerpt">Короткое описание (excerpt)</label>
    <textarea
        id="excerpt"
        name="excerpt"
        rows="3"
        class="form-control @error('excerpt') is-invalid @enderror"
    >{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
    @error('excerpt')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="content">Текст новости</label>
    <textarea
        id="content"
        name="content"
        rows="10"
        class="form-control @error('content') is-invalid @enderror"
        required
    >{{ old('content', $article->content ?? '') }}</textarea>
    @error('content')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="published_at">Дата публикации</label>
    <input
        id="published_at"
        type="datetime-local"
        name="published_at"
        class="form-control @error('published_at') is-invalid @enderror"
        value="{{ old('published_at', isset($article) && $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '') }}"
    >
    <small class="form-text text-muted">Можно оставить пустым.</small>
    @error('published_at')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
