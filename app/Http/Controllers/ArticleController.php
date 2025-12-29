<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Список новостей (пагинация).
     */
    public function index(Request $request)
    {
        $articles = Article::query()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('articles.index', [
            'articles' => $articles,
        ]);
    }

    /**
     * Форма создания новости.
     */
    public function create()
    {
        return view('articles.create', [
            'article' => new Article(),
        ]);
    }

    /**
     * Сохранение созданной новости.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string', 'min:10'],
            'published_at' => ['nullable', 'date'],
        ]);

        $article = Article::create($data);

        return redirect()
            ->route('articles.show', $article)
            ->with('status', 'Новость успешно создана');
    }

    /**
     * Просмотр одной новости.
     */
    public function show(Article $article)
    {
        return view('articles.show', [
            'article' => $article,
        ]);
    }

    /**
     * Форма редактирования.
     */
    public function edit(Article $article)
    {
        return view('articles.edit', [
            'article' => $article,
        ]);
    }

    /**
     * Обновление новости.
     */
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
            ->with('status', 'Новость успешно обновлена');
    }

    /**
     * Удаление новости.
     */
    public function destroy(Article $article)
    {
        $article->delete();

        return redirect()
            ->route('articles.index')
            ->with('status', 'Новость удалена');
    }
}
