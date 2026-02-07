<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function open(Request $request, string $id)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $n = $user->notifications()->where('id', $id)->first();
        if (!$n) {
            return redirect('/articles')->with('error', 'Уведомление не найдено.');
        }

        $n->markAsRead();

        $articleId = $n->data['article_id'] ?? null;
        if ($articleId && \Illuminate\Support\Facades\Route::has('articles.show')) {
            return redirect()->route('articles.show', $articleId);
        }

        return redirect('/articles');
    }
}
