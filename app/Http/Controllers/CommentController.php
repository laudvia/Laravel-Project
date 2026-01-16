<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Comment::class, 'comment');
    }

    public function index(Article $article)
    {
        $comments = $article->comments()
            ->latest()
            ->paginate(10);

        return view('comments.index', [
            'article' => $article,
            'comments' => $comments,
        ]);
    }

    public function create(Article $article)
    {
        return view('comments.create', [
            'article' => $article,
            'comment' => new Comment(),
        ]);
    }

    public function store(Request $request, Article $article)
    {
        $data = $request->validate([
            'author_name'  => ['required', 'string', 'min:2', 'max:100'],
            'author_email' => ['nullable', 'email', 'max:150'],
            'body'         => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $article->comments()->create($data);

        return redirect()
            ->route('articles.show', $article)
            ->with('success', 'Комментарий добавлен.');
    }

    public function edit(Comment $comment)
    {
        $comment->load('article');

        return view('comments.edit', [
            'comment' => $comment,
            'article' => $comment->article,
        ]);
    }

    public function update(Request $request, Comment $comment)
    {
        $data = $request->validate([
            'author_name'  => ['required', 'string', 'min:2', 'max:100'],
            'author_email' => ['nullable', 'email', 'max:150'],
            'body'         => ['required', 'string', 'min:3', 'max:2000'],
        ]);

        $comment->update($data);

        return redirect()
            ->route('articles.show', $comment->article)
            ->with('success', 'Комментарий обновлён.');
    }

    public function destroy(Comment $comment)
    {
        $article = $comment->article;

        $comment->delete();

        return redirect()
            ->route('articles.show', $article)
            ->with('success', 'Комментарий удалён.');
    }
}
