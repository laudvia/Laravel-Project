<?php

namespace App\Http\Controllers;

use App\Events\NewArticleEvent;
use App\Jobs\VeryLongJob;
use App\Models\Article;
use App\Models\User;
use App\Notifications\NewArticleCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ArticleController extends Controller
{
    public function __construct()
    {
        // Привязываем policy к ресурсному контроллеру
        $this->authorizeResource(Article::class, 'article');
    }

    public function index()
    {
        $articles = Article::query()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(10);

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
        return view('articles.show', [
            'article' => $article,
        ]);
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

        return redirect()
            ->route('articles.show', $article)
            ->with('success', 'Статья обновлена.');
    }

    public function destroy(Article $article)
    {
        $article->delete();

        return redirect()
            ->route('articles.index')
            ->with('success', 'Статья удалена (комментарии удаляются автоматически).');
    }
}
