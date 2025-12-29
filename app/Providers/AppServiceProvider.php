<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Чтобы пагинация выглядела нормально с Bootstrap (в шаблоне подключён Bootstrap 4.3)
        Paginator::useBootstrapFour();
    }
}
