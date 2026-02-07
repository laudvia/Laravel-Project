<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class CommentManageController extends Controller
{
    public function edit(Comment $comment)
    {
        $this->authorize('update', $comment);

        return view('comments.edit', [
            'comment' => $comment,
        ]);
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:2000'],
        ]);

        $user = $request->user();
        $isModerator = Gate::forUser($user)->allows('is-moderator');

        $comment->body = $validated['body'];

        // Если НЕ модератор — после редактирования снова на модерацию
        $comment->is_approved = $isModerator ? true : false;

        $comment->save();

        // Сброс кэша страницы статьи (блок "часть комментариев")
        Cache::forget("laravel_cache_articles.show." . $comment->article_id);

        return redirect()
            ->route('articles.show', $comment->article_id)
            ->with('success', $isModerator
                ? 'Комментарий обновлён (модератор).'
                : 'Комментарий обновлён и отправлен на модерацию.'
            );
    }

    public function destroy(Request $request, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $articleId = (int) $comment->article_id;
        $comment->delete();

        Cache::forget("laravel_cache_articles.show." . $articleId);

        return redirect()
            ->route('articles.show', $articleId)
            ->with('success', 'Комментарий удалён.');
    }
}
