<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function index(Article $article)
    {
        $comments = $article->comments()
            ->with('author')
            ->latest()
            ->paginate(10);

        return view('comments.index', [
            'article' => $article,
            'comments' => $comments,
        ]);
    }

    public function create(Article $article)
    {
        Gate::authorize('comment.create');

        return view('comments.create', [
            'article' => $article,
            'comment' => new Comment(),
        ]);
    }

    public function store(Request $request, Article $article)
    {
        Gate::authorize('comment.create');

        $data = $request->validate([
            'body' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $user = $request->user();

        $article->comments()->create([
            'user_id' => $user->id,
            // Дублируем имя/почту для удобного вывода (и чтобы комментарий не "сломался",
            // если у пользователя поменяются данные)
            'author_name' => $user->name,
            'author_email' => $user->email,
            'body' => $data['body'],
        ]);

        return redirect()
            ->route('articles.show', $article)
            ->with('success', 'Комментарий добавлен.');
    }

    public function edit(Comment $comment)
    {
        Gate::authorize('comment.update', $comment);

        $comment->load('article');

        return view('comments.edit', [
            'comment' => $comment,
            'article' => $comment->article,
        ]);
    }

    public function update(Request $request, Comment $comment)
    {
        Gate::authorize('comment.update', $comment);

        $data = $request->validate([
            'body' => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $comment->update([
            'body' => $data['body'],
        ]);

        return redirect()
            ->route('articles.show', $comment->article)
            ->with('success', 'Комментарий обновлён.');
    }

    public function destroy(Comment $comment)
    {
        Gate::authorize('comment.delete', $comment);

        $article = $comment->article;

        $comment->delete();

        return redirect()
            ->route('articles.show', $article)
            ->with('success', 'Комментарий удалён.');
    }
}
