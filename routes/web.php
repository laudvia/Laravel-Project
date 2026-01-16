<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;

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
Route::resource('articles', ArticleController::class)
    ->only(['index', 'show'])
    ->whereNumber('article');

// Все изменения данных (создание/редактирование/удаление/комментарии) — только для авторизованных.
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('articles', ArticleController::class)
        ->except(['index', 'show'])
        ->whereNumber('article');

    Route::resource('articles.comments', CommentController::class)
        ->shallow()
        ->except(['show'])
        ->whereNumber('article');
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
