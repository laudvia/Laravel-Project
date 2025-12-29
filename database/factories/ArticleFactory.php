<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        $title = fake()->sentence(6, true);

        return [
            'title' => rtrim($title, '.'),
            'excerpt' => fake()->sentence(18, true),
            'content' => implode("\n\n", fake()->paragraphs(6)),
            'published_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
