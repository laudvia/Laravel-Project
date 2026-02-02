<?php

namespace App\Notifications;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewArticleCreatedNotification extends Notification
{
    use Queueable;

    public readonly int $articleId;
    public readonly string $title;

    public function __construct(Article $article)
    {
        $this->articleId = (int) $article->id;
        $this->title = (string) $article->title;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'article_id' => $this->articleId,
            'title' => $this->title,
        ];
    }
}
