<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Список новостей (чтение из БД).
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
}
