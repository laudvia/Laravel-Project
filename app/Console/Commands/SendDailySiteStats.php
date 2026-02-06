<?php

namespace App\Console\Commands;

use App\Mail\DailySiteStatsMail;
use App\Models\ArticleView;
use App\Models\Comment;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class SendDailySiteStats extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'site:daily-stats {--date= : Дата в формате YYYY-MM-DD (по умолчанию сегодня)}';

    /**
     * The console command description.
     */
    protected $description = 'Отправляет модераторам статистику использования сайта за день (просмотры и комментарии)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dateStr = (string) ($this->option('date') ?: '');
        $date = $dateStr !== '' ? Carbon::parse($dateStr)->startOfDay() : now()->startOfDay();

        $viewsCount = ArticleView::query()
            ->whereBetween('created_at', [$date, $date->copy()->endOfDay()])
            ->count();

        $commentsCount = Comment::query()
            ->whereBetween('created_at', [$date, $date->copy()->endOfDay()])
            ->count();

        $topArticles = ArticleView::query()
            ->selectRaw('article_id, COUNT(*) as views')
            ->whereNotNull('article_id')
            ->whereBetween('created_at', [$date, $date->copy()->endOfDay()])
            ->groupBy('article_id')
            ->orderByDesc('views')
            ->limit(5)
            ->get()
            ->map(fn ($r) => ['article_id' => (int) $r->article_id, 'views' => (int) $r->views])
            ->all();

        // Получаем email модераторов из БД (роль slug=moderator)
        $emails = [];
        $role = Role::query()->where('slug', 'moderator')->first();
        if ($role) {
            $emails = $role->users()->pluck('email')->filter()->unique()->values()->all();
        }

        // Fallback: из config/mail.php (если в БД модераторов нет)
        if (empty($emails)) {
            $cfg = (string) (config('mail.moderator.address') ?: config('mail.from.address'));
            if ($cfg !== '') {
                $emails = [$cfg];
            }
        }

        if (empty($emails)) {
            $this->warn('Не найдено ни одного email модератора для отправки статистики.');
            return self::SUCCESS;
        }

        $mail = new DailySiteStatsMail(
            date: $date->format('Y-m-d'),
            viewsCount: $viewsCount,
            commentsCount: $commentsCount,
            topArticles: $topArticles,
        );

        try {
            Mail::to($emails)->send($mail);
        } catch (\Throwable $e) {
            $this->error('Ошибка отправки письма: '.$e->getMessage());
            return self::FAILURE;
        }

        $this->info('Статистика отправлена: '.implode(', ', $emails));
        $this->line('Просмотры: '.$viewsCount.'; Комментарии: '.$commentsCount);

        return self::SUCCESS;
    }
}
