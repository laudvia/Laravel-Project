<?php

namespace App\Jobs;

use App\Mail\NewArticleCreated;
use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VeryLongJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Article $article)
    {
        // Можно настроить очередь/приоритет, если нужно:
        // $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Имитация "долгой" задачи (не обязательно):
        // sleep(5);

        $moderatorEmail = (string) (config('mail.moderator.address') ?: config('mail.from.address'));
        if ($moderatorEmail === '') {
            return;
        }

        try {
            Mail::to($moderatorEmail)->send(new NewArticleCreated($this->article));
        } catch (\Throwable $e) {
            // Не падаем — статья уже создана, а уведомление можно повторить позже.
            Log::warning('Failed to send new article notification email (queued)', [
                'article_id' => $this->article->id,
                'to' => $moderatorEmail,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
