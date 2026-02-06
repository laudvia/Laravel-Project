<?php

namespace App\Http\Middleware;

use App\Models\ArticleView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogArticleView
{
    /**
     * Handle an incoming request.
     *
     * Сохраняем факт просмотра статьи (URL + метаданные) в БД.
     * ВАЖНО: IP-адрес намеренно не сохраняем.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Логируем только успешные "просмотры" страницы статьи (обычно GET 200).
        // Если нужно логировать и 304/redirect — уберите проверки ниже.
        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            try {
                $articleParam = $request->route('article');
                $articleId = null;

                // resource route binding может передавать модель Article или id
                if (is_object($articleParam) && property_exists($articleParam, 'id')) {
                    $articleId = $articleParam->id;
                } elseif (is_numeric($articleParam)) {
                    $articleId = (int) $articleParam;
                }

                ArticleView::create([
                    'article_id' => $articleId,
                    'user_id' => $request->user()?->id,
                    'method' => $request->method(),
                    'path' => '/' . ltrim($request->path(), '/'),
                    'full_url' => $request->fullUrl(),
                ]);
            } catch (\Throwable $e) {
                // Не блокируем показ статьи из-за логирования.
                // При желании можно залогировать в Log::warning(...)
            }
        }

        return $response;
    }
}
