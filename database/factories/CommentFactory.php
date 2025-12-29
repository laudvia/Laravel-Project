<?php

namespace Database\Factories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            // article_id задаём при создании (в сидере или при вызове factory)
            'article_id' => null,
            'author_name' => fake()->name(),
            'author_email' => fake()->boolean(70) ? fake()->safeEmail() : null,
            'body' => fake()->paragraphs(2, true),
        ];
    }
}
