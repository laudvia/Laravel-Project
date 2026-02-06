<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [MainController::class, 'index']);

/*
|--------------------------------------------------------------------------
| ЛР6: Регистрация / Авторизация / Выход (Sanctum + Auth)
|--------------------------------------------------------------------------
*/

// Backward-compat со старыми URL из предыдущих ЛР
Route::redirect('/signup', '/register');
Route::redirect('/auth/login', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
    Route::post('/register', [AuthController::class, 'register'])->name('register');

    // Важно: middleware Authenticate ожидает route name "login" для редиректа неавторизованных.
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum')
    ->name('logout');

// Новости (Article) + Comments
// Публично доступны только просмотр списка и отдельной новости.
//
// ВАЖНО: whereNumber('article') фиксит конфликт /articles/create,
// который иначе перехватывается роутом /articles/{article} как {article}="create".
// Публично доступны: список + просмотр отдельной статьи.
// Просмотр статьи логируем через middleware (ЛР14).
Route::get('/articles', [ArticleController::class, 'index'])
    ->name('articles.index');

Route::get('/articles/{article}', [ArticleController::class, 'show'])
    ->name('articles.show')
    ->middleware('log.article.view')
    ->whereNumber('article');

// Все изменения данных (создание/редактирование/удаление/комментарии) — только для авторизованных.
Route::middleware('auth:sanctum')->group(function () {
    // ЛР12: открыть уведомление (пометить как прочитанное) и перейти к статье
    Route::get('/notifications/{notification}', [NotificationController::class, 'open'])
        ->name('notifications.open');

    Route::resource('articles', ArticleController::class)
        ->except(['index', 'show'])
        ->whereNumber('article');

    Route::resource('articles.comments', CommentController::class)
        ->shallow()
        ->except(['show'])
        ->whereNumber('article');
});

/*
|--------------------------------------------------------------------------
| ЛР9: Модерация комментариев (только модератор)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'can:is-moderator'])
    ->prefix('moderator')
    ->name('moderator.')
    ->group(function () {
        Route::get('/comments', [CommentController::class, 'moderationIndex'])
            ->name('comments.index');

        Route::patch('/comments/{comment}/approve', [CommentController::class, 'approve'])
            ->name('comments.approve')
            ->whereNumber('comment');

        Route::delete('/comments/{comment}/reject', [CommentController::class, 'reject'])
            ->name('comments.reject')
            ->whereNumber('comment');
    });

Route::redirect('/news', '/articles');

Route::get('/galery/{full_image}', [MainController::class, 'show']);

Route::get('/about', function () {
    return view('main/about');
});

Route::get('/contact', function () {
    $contact = [
        'name' => 'Polytech',
        'adress' => 'B.Semenovskaya',
        'phone' => '8(495) 423-2323',
        'email' => '@mospolythech.ru'
    ];

    return view('main/contact', ['contact' => $contact]);
});
