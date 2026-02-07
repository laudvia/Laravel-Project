<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class WebArticleCommentController extends Controller
{
    public function index(Article $article)
    {
        $commentsQuery = Comment::query()
            ->where('article_id', $article->id)
            ->orderByDesc('created_at');

        // Если в таблице есть флаг модерации — показываем всем только одобренные, а модератору — все
        if (Schema::hasColumn('comments', 'is_approved') && !Auth::user()?->can('is-moderator')) {
            $commentsQuery->where('is_approved', true);
        }
        if (Schema::hasColumn('comments', 'approved') && !Auth::user()?->can('is-moderator')) {
            $commentsQuery->where('approved', true);
        }

        $comments = $commentsQuery->paginate(15);

        return view('articles.comments', compact('article', 'comments'));
    }

    public function store(Request $request, Article $article)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Нужно войти, чтобы оставить комментарий.');
        }

        $data = $request->validate([
            'content' => ['required', 'string', 'min:2', 'max:2000'],
        ]);

        $comment = new Comment();

        // привязка к статье
        if (Schema::hasColumn('comments', 'article_id')) {
            $comment->setAttribute('article_id', $article->id);
        }

        // автор
        if (Schema::hasColumn('comments', 'user_id')) {
            $comment->setAttribute('user_id', Auth::id());
        }

        // имя автора (если есть поле)
        foreach (['author_name','username','name'] as $col) {
            if (Schema::hasColumn('comments', $col)) {
                $comment->setAttribute($col, Auth::user()->name);
                break;
            }
        }

        // текст комментария — подстроимся под возможные названия колонок
        $text = $data['content'];
        $textCols = ['content','body','text','message','comment','comment_text'];
        $written = false;
        foreach ($textCols as $col) {
            if (Schema::hasColumn('comments', $col)) {
                $comment->setAttribute($col, $text);
                $written = true;
                break;
            }
        }
        if (!$written) {
            // fallback
            $comment->setAttribute('content', $text);
        }

        // модерация:
        // модератор — сразу approved
        // обычный — на проверку
        $isModerator = Auth::user()->can('is-moderator');

        if (Schema::hasColumn('comments', 'is_approved')) {
            $comment->setAttribute('is_approved', $isModerator ? true : false);
        }
        if (Schema::hasColumn('comments', 'approved')) {
            $comment->setAttribute('approved', $isModerator ? true : false);
        }
        if (Schema::hasColumn('comments', 'status')) {
            $comment->setAttribute('status', $isModerator ? 'approved' : 'pending');
        }

        try {
            $comment->save();
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Не удалось сохранить комментарий: '.$e->getMessage());
        }

        return redirect()
            ->route('articles.comments.index', $article)
            ->with('success', $isModerator
                ? 'Комментарий добавлен (модератор).'
                : 'Комментарий отправлен на проверку модератору.');
    }
}
