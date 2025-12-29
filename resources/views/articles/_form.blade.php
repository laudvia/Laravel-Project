<div class="form-group">
    <label for="title">Заголовок</label>
    <input
        type="text"
        id="title"
        name="title"
        class="form-control @error('title') is-invalid @enderror"
        value="{{ old('title', $article->title) }}"
        required
    >
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="excerpt">Краткое описание (необязательно)</label>
    <textarea
        id="excerpt"
        name="excerpt"
        rows="2"
        class="form-control @error('excerpt') is-invalid @enderror"
    >{{ old('excerpt', $article->excerpt) }}</textarea>
    @error('excerpt')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="content">Текст</label>
    <textarea
        id="content"
        name="content"
        rows="8"
        class="form-control @error('content') is-invalid @enderror"
        required
    >{{ old('content', $article->content) }}</textarea>
    @error('content')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="published_at">Дата публикации (необязательно)</label>
    <input
        type="datetime-local"
        id="published_at"
        name="published_at"
        class="form-control @error('published_at') is-invalid @enderror"
        value="{{ old('published_at', optional($article->published_at)->format('Y-m-d\TH:i')) }}"
    >
    @error('published_at')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
