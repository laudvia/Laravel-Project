<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use App\Policies\ArticlePolicy;
use App\Policies\CommentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Article::class => ArticlePolicy::class,
        Comment::class => CommentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Хук шлюза: модератор проходит прежде всех остальных проверок.
        // Если вернули true — разрешаем любое действие, не вызывая policy/gate дальше.
        Gate::before(function (?User $user, string $ability) {
            if (!$user) {
                return null;
            }

            if ($user->isModerator()) {
                return true;
            }

            return null; // продолжаем обычные проверки (policy/gate)
        });

        Gate::define('is-moderator', fn (User $user) => $user->isModerator());
    }
}
