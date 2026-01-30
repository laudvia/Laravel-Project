<?php

namespace App\Events;

use App\Models\Article;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewArticleEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Article $article;

    /**
     * @param  Article  $article  Новая статья, о которой нужно уведомить пользователей онлайн.
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * Канал вещания (публичный).
     */
    public function broadcastOn(): Channel
    {
        return new Channel('test');
    }

    /**
     * Данные, которые попадут в JS (payload).
     */
    public function broadcastWith(): array
    {
        return [
            'article' => [
                'id' => $this->article->id,
                'title' => $this->article->title,
                'excerpt' => $this->article->excerpt,
                'published_at' => optional($this->article->published_at)?->toDateTimeString(),
            ],
        ];
    }
}
