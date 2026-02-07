<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;

class ArticleCommentController extends Controller
{
    public function index(Article $article)
    {
        // Показываем только одобренные + свои (если пользователь залогинен)
        $q = Comment::query()->where('article_id', $article->id)->latest();

        if (auth()->check()) {
            $q->where(function ($qq) {
                $qq->where('is_approved', true)
                   ->orWhere('user_id', auth()->id());
            });
        } else {
            $q->where('is_approved', true);
        }

        $comments = $q->paginate(20);

        return view('articles.comments.index', compact('article', 'comments'));
    }

    public function store(Request $request, Article $article)
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:2000'],
        ]);

        $isModerator = Gate::allows('is-moderator');

        $comment = new Comment();
        $comment->article_id = $article->id;
        $comment->body = $validated['body'];
        $comment->user_id = auth()->id();
        $comment->author_name = auth()->user()->name ?? 'Гость';
        $comment->is_approved = $isModerator ? true : false;
        $comment->save();

        
        // Сброс кэша страницы статьи, чтобы на /articles/{id} обновился блок 'часть комментариев'
        Cache::forget("laravel_cache_articles.show." . $article->id);
return back()->with(
            'success',
            $isModerator
                ? 'Комментарий опубликован (модератор).'
                : 'Комментарий отправлен на модерацию.'
        );
    }
}
