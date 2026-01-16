<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Кастомный ответ для запрета доступа из Gate/Policy
        $this->renderable(function (AuthorizationException $e, Request $request) {
            $message = $e->getMessage() ?: 'Доступ запрещён.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                ], Response::HTTP_FORBIDDEN);
            }

            return response()->view('errors.403', [
                'message' => $message,
            ], Response::HTTP_FORBIDDEN);
        });

        // Кастомный ответ для неавторизованных (auth middleware)
        $this->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Требуется авторизация.',
                ], Response::HTTP_UNAUTHORIZED);
            }

            return redirect()->route('login')->with('error', 'Войдите в систему, чтобы продолжить.');
        });
    }
}
