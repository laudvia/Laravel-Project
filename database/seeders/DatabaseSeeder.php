<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // Articles + comments (учебный пример)
        \App\Models\Article::factory(20)->create()->each(function ($article) {
            \App\Models\Comment::factory(fake()->numberBetween(0, 6))
                ->create(['article_id' => $article->id]);
        });
    }
}
