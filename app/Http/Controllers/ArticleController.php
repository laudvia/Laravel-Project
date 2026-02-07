<?php

namespace App\Http\Controllers;




use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\ArticleView;
use App\Events\NewArticleEvent;
use App\Jobs\VeryLongJob;
use App\Models\Article;
use App\Models\User;
use App\Notifications\NewArticleCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

use App\Models\Comment;
use Illuminate\Support\Facades\Gate;
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
        Cache::increment('stats:views:' . now()->toDateString());
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
        // --- Record article view (for daily stats) ---
        try {
            $viewQ = ArticleView::query()->where('article_id', $article->id);

            if (auth()->check()) {
                $viewQ->where('user_id', auth()->id());
            } else {
                if (DB::getSchemaBuilder()->hasColumn((new ArticleView())->getTable(), 'session_id')) {
                    $viewQ->where('session_id', session()->getId());
                }
            }

            $viewQ->whereDate('created_at', now()->toDateString());

            if (!$viewQ->exists()) {
                $data = [
                    'article_id' => $article->id,
                    'user_id'    => auth()->id(),
                    'method'     => request()->method(),
                    'path'       => request()->path() ? '/' . ltrim(request()->path(), '/') : request()->getPathInfo(),
                    'full_url'   => request()->fullUrl(),
                ];

                if (DB::getSchemaBuilder()->hasColumn((new ArticleView())->getTable(), 'session_id')) {
                    $data['session_id'] = session()->getId();
                }

                ArticleView::create($data);
            }
        } catch (\Throwable $e) {
            // no-op
        }
        $this->recordArticleView($article);
        Cache::increment('stats:views:' . now()->toDateString());
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

    /**
     * Записывает просмотр статьи в таблицу article_views (для статистики).
     * Не использует IP. Дедупликация: 1 просмотр/сутки на сессию (и пользователя, если есть user_id).
     */
    private function recordArticleView(\App\Models\Article $article): void
    {
        try {
            $table = (new ArticleView())->getTable();

            if (!Schema::hasTable($table)) {
                return;
            }

            $now = now();
            $data = ['article_id' => $article->id];

            // Пишем только если колонки реально есть
            if (Schema::hasColumn($table, 'user_id')) {
                $data['user_id'] = auth()->id();
            }
            if (Schema::hasColumn($table, 'session_id')) {
                $data['session_id'] = session()->getId();
            }
            if (Schema::hasColumn($table, 'viewed_at')) {
                $data['viewed_at'] = $now;
            }
            if (Schema::hasColumn($table, 'created_at')) {
                $data['created_at'] = $now;
            }
            if (Schema::hasColumn($table, 'updated_at')) {
                $data['updated_at'] = $now;
            }

            // Дедупликация: если есть session_id и/или viewed_at — не плодим записи
            $q = DB::table($table)->where('article_id', $article->id);

            if (Schema::hasColumn($table, 'session_id')) {
                $q->where('session_id', session()->getId());
            }
            if (Schema::hasColumn($table, 'user_id') && auth()->check()) {
                $q->where('user_id', auth()->id());
            }
            if (Schema::hasColumn($table, 'viewed_at')) {
                $q->whereDate('viewed_at', $now->toDateString());
            } elseif (Schema::hasColumn($table, 'created_at')) {
                $q->whereDate('created_at', $now->toDateString());
            }

            if ($q->exists()) {
                return;
            }

            DB::table($table)->insert($data);
        } catch (\Throwable $e) {
            // Не ломаем страницу из-за статистики
        }
    }

}
