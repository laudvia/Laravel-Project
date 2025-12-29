<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;

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
Route::get('/signup', [AuthController::class, 'create']);
Route::post('/auth/login', [AuthController::class, 'signUp']);
Route::get('/', [MainController::class, 'index']);

// Новости из БД (модель Article)
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
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
