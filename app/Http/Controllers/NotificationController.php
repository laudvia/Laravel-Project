<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function open(Request $request, string $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->where("id", $id)->firstOrFail();
        $notification->markAsRead();

        $articleId = $notification->data["article_id"] ?? null;
        if ($articleId) {
            return redirect()->route("articles.show", $articleId)
                ->with("success", "Уведомление открыто.");
        }

        return back()->with("success", "Уведомление открыто.");
    }
}
