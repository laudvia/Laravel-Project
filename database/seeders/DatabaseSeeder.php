<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        $moderator = User::query()->where('email', 'moderator@example.com')->first();

        // Articles + comments (учебный пример)
        Article::factory(20)->create([
            'user_id' => $moderator?->id,
        ])->each(function (Article $article) {
            Comment::factory(fake()->numberBetween(0, 6))
                ->create(['article_id' => $article->id]);
        });
    }
}
