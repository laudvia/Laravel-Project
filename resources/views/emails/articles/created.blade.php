<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Новая статья</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.5; color: #111;">
    <div style="max-width: 640px; margin: 0 auto; padding: 16px;">
        <h1 style="margin: 0 0 12px; font-size: 20px;">
            Добавлена новая статья
        </h1>

        <p style="margin: 0 0 10px;">
            <strong>Заголовок:</strong>
            {{ $article->title }}
        </p>

        @if(!empty($article->excerpt))
            <p style="margin: 0 0 10px;">
                <strong>Краткое описание:</strong><br>
                {{ $article->excerpt }}
            </p>
        @endif

        <p style="margin: 0 0 10px;">
            <strong>Дата публикации:</strong>
            {{ $article->published_at ? $article->published_at->format('d.m.Y H:i') : 'Не указана' }}
        </p>

        <p style="margin: 0 0 10px;">
            <strong>Автор:</strong>
            {{ optional($article->user)->name ?? 'Не указан' }}
        </p>

        <div style="margin: 16px 0 0; padding: 12px; border: 1px solid #e5e7eb; border-radius: 10px;">
            <div style="font-size: 12px; color: #6b7280; margin-bottom: 6px;">Фрагмент текста</div>
            <div style="white-space: pre-wrap;">
                {{ \Illuminate\Support\Str::limit(strip_tags($article->content), 800) }}
            </div>
        </div>

        <p style="margin: 18px 0 0;">
            <a href="{{ route('articles.show', $article) }}"
               style="display: inline-block; padding: 10px 14px; text-decoration: none; border-radius: 10px; border: 1px solid #111; color: #111;">
                Открыть статью на сайте
            </a>
        </p>

        <hr style="margin: 24px 0; border: 0; border-top: 1px solid #e5e7eb;">
        <p style="margin: 0; font-size: 12px; color: #6b7280;">
            Это автоматическое уведомление от «{{ config('app.name') }}».
        </p>
    </div>
</body>
</html>
