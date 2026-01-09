<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function update(User $user, Article $article): bool
    {
        return (int) $article->user_id === (int) $user->id;
    }

    public function delete(User $user, Article $article): bool
    {
        return (int) $article->user_id === (int) $user->id;
    }
}
