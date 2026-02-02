<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Открывает статью из уведомления и отмечает его как прочитанное.
     */
    public function open(Request $request, string $notificationId): RedirectResponse
    {
        $user = $request->user();

        // Берём уведомление только из набора уведомлений текущего пользователя,
        // чтобы исключить доступ к чужим уведомлениям.
        $notification = $user->notifications()->where('id', $notificationId)->firstOrFail();

        // Помечаем как прочитанное.
        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        $articleId = (int) ($notification->data['article_id'] ?? 0);

        return redirect()->route('articles.show', $articleId);
    }
}
