<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    // GET /api/articles
    public function index(Request $request)
    {
        $perPage = (int) $request->query("per_page", 10);
        $perPage = max(1, min(50, $perPage));

        $articles = Article::query()
            ->orderByDesc("published_at")
            ->orderByDesc("id")
            ->paginate($perPage);

        return response()->json($articles, 200);
    }

    // GET /api/articles/{article}
    public function show(Article $article)
    {
        if (method_exists($article, "comments")) {
            $article->load(["comments" => function ($q) {
                $q->orderByDesc("id");
            }]);
        }

        return response()->json([
            "article" => $article,
        ], 200);
    }

    // POST /api/articles (auth:sanctum)
    public function store(Request $request)
    {
        $validated = $request->validate([
            "title" => ["required", "string", "max:255"],
            "excerpt" => ["nullable", "string", "max:1000"],
            "content" => ["required", "string", "min:10"],
            "published_at" => ["nullable", "date"],
        ]);

        $article = new Article($validated);

        if ($request->user() && (property_exists($article, "user_id") || in_array("user_id", $article->getFillable(), true))) {
            $article->user_id = $request->user()->id;
        }

        $article->save();

        return response()->json([
            "message" => "Article created.",
            "article" => $article,
        ], 201);
    }

    // PUT/PATCH /api/articles/{article} (auth:sanctum)
    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            "title" => ["sometimes", "required", "string", "max:255"],
            "excerpt" => ["sometimes", "nullable", "string", "max:1000"],
            "content" => ["sometimes", "required", "string", "min:10"],
            "published_at" => ["sometimes", "nullable", "date"],
        ]);

        $article->fill($validated);
        $article->save();

        return response()->json([
            "message" => "Article updated.",
            "article" => $article,
        ], 200);
    }

    // DELETE /api/articles/{article} (auth:sanctum)
    public function destroy(Article $article)
    {
        $article->delete();

        return response()->json([
            "message" => "Article deleted.",
        ], 200);
    }
}
