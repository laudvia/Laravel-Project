<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        $userId = User::query()->inRandomOrder()->value('id');

        return [
            // article_id задаём при создании (в сидере или при вызове factory)
            'article_id' => null,
            'user_id' => $userId,
            // дублируем имя/почту, чтобы было удобно выводить, даже если пользователь изменится
            'author_name' => $userId ? (User::query()->find($userId)?->name ?? fake()->name()) : fake()->name(),
            'author_email' => $userId ? (User::query()->find($userId)?->email ?? null) : (fake()->boolean(70) ? fake()->safeEmail() : null),
            'body' => fake()->paragraphs(2, true),
        ];
    }
}
