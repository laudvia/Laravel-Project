<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Статистика сайта</title>
</head>
<body>
<h2>Статистика использования сайта за {{ $date }}</h2>

<ul>
    <li><strong>Просмотры новостей:</strong> {{ $viewsCount }}</li>
    <li><strong>Новые комментарии:</strong> {{ $commentsCount }}</li>
</ul>

@if(!empty($topArticles))
    <h3>Топ-5 статей по просмотрам</h3>
    <ol>
        @foreach($topArticles as $row)
            <li>
                ID: {{ $row['article_id'] }} — {{ $row['views'] }}
            </li>
        @endforeach
    </ol>
@endif

<p style="margin-top:16px;color:#666;font-size:12px;">
    Письмо сформировано автоматически планировщиком задач Laravel.
</p>
</body>
</html>
