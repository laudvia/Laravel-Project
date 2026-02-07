<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class CommentPolicy
{
    public function update(User $user, Comment $comment): bool
    {
        if (Gate::forUser($user)->allows('is-moderator')) {
            return true;
        }

        return (int) $comment->user_id === (int) $user->id;
    }

    public function delete(User $user, Comment $comment): bool
    {
        if (Gate::forUser($user)->allows('is-moderator')) {
            return true;
        }

        return (int) $comment->user_id === (int) $user->id;
    }
}
