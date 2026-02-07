<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ArticleController;

// Auth (API)
Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"]);
Route::post("/logout", [AuthController::class, "logout"])->middleware("auth:sanctum");

// Articles (API)
Route::get("/articles", [ArticleController::class, "index"]);
Route::get("/articles/{article}", [ArticleController::class, "show"]);

Route::middleware("auth:sanctum")->group(function () {
    Route::post("/articles", [ArticleController::class, "store"]);
    Route::match(["put", "patch"], "/articles/{article}", [ArticleController::class, "update"]);
    Route::delete("/articles/{article}", [ArticleController::class, "destroy"]);
});
