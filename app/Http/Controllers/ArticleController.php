<?php

namespace App\Http\Controllers;

use App\Events\NewArticleEvent;
use App\Jobs\VeryLongJob;
use App\Models\Article;
use App\Models\User;
use App\Notifications\NewArticleCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class ArticleController extends Controller
{
    /**
     * Ключ, под которым храним список страниц пагинации, которые уже были закешированы в index().
     * Нужен, чтобы в store() корректно очистить кэш по всем страницам списка.
     */
    private const INDEX_PAGES_CACHE_KEY = 'articles.index.cached_pages';

    public function __construct()
    {
        // Привязываем policy к ресурсному контроллеру
        $this->authorizeResource(Article::class, 'article');
    }

    public function index()
    {
        $page = (int) request()->query('page', 1);

        $cacheKey = "articles.index.page.$page";
        $articles = Cache::remember($cacheKey, now()->addMinutes(5), function () {
            return Article::query()
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->paginate(10);
        });

        // Запоминаем, какие страницы пагинации уже кэшировали, чтобы уметь их чистить в store().
        $pages = Cache::get(self::INDEX_PAGES_CACHE_KEY, []);
        if (!in_array($page, $pages, true)) {
            $pages[] = $page;
            Cache::put(self::INDEX_PAGES_CACHE_KEY, $pages, now()->addDay());
        }

        return view('articles.index', [
            'articles' => $articles,
        ]);
    }

    public function create()
    {
        return view('articles.create', [
            'article' => new Article(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string', 'min:10'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['user_id'] = $request->user()?->id;

        $article = Article::create($data);

        // ЛР13: после создания статьи чистим кэш главной (списка) включая страницы пагинации.
        $this->clearIndexCache();

        // ЛР12: уведомление читателей (database notifications)
        // Отправляем только пользователям с ролью reader.
        // Они, как правило, не аутентифицированы в текущей сессии (создаёт новость модератор).
        $readers = User::query()
            ->whereHas('role', fn ($q) => $q->where('slug', 'reader'))
            ->where('id', '!=', $request->user()->id)
            ->get();

        Notification::send($readers, new NewArticleCreatedNotification($article));

        // Онлайн-оповещение пользователей, которые сейчас находятся на сайте
        event(new NewArticleEvent($article));

        // Уведомление модератора выносим в очередь (database driver)
        VeryLongJob::dispatch($article);

        // Чтобы не было "мелькания" (лишнего редиректа через страницу show),
        // после создания всегда уходим на список статей.
        return redirect()
            ->route('articles.index')
            ->with('success', 'Статья создана.');
    }

    public function show(Article $article)
    {
        // ЛР13: кэшируем страницу просмотра статьи вместе с комментариями.
        // Используем rememberForever, т.к. в требованиях указано «rememberForever».
        $cacheKey = "articles.show.{$article->id}";

        $payload = Cache::rememberForever($cacheKey, function () use ($article) {
            // Берём свежие данные из БД и сразу загружаем связи.
            $freshArticle = Article::query()
                ->with('user')
                ->findOrFail($article->id);

            $latestComments = $freshArticle
                ->comments()
                ->approved()
                ->with('author')
                ->latest()
                ->take(5)
                ->get();

            return [
                'article' => $freshArticle,
                'latestComments' => $latestComments,
            ];
        });

        return view('articles.show', $payload);
    }

    public function edit(Article $article)
    {
        return view('articles.edit', [
            'article' => $article,
        ]);
    }

    public function update(Request $request, Article $article)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string', 'min:10'],
            'published_at' => ['nullable', 'date'],
        ]);

        $article->update($data);

        // ЛР13: при обновлении чистим весь кэш.
        Cache::flush();

        return redirect()
            ->route('articles.show', $article)
            ->with('success', 'Статья обновлена.');
    }

    public function destroy(Article $article)
    {
        $article->delete();

        // ЛР13: при удалении чистим весь кэш.
        Cache::flush();

        return redirect()
            ->route('articles.index')
            ->with('success', 'Статья удалена (комментарии удаляются автоматически).');
    }

    private function clearIndexCache(): void
    {
        $pages = Cache::get(self::INDEX_PAGES_CACHE_KEY, []);

        // На всякий случай всегда очищаем первую страницу.
        if (!in_array(1, $pages, true)) {
            $pages[] = 1;
        }

        foreach ($pages as $page) {
            Cache::forget("articles.index.page.$page");
        }

        Cache::forget(self::INDEX_PAGES_CACHE_KEY);
    }
}
