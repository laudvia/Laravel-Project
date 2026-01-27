<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * Список/просмотр комментариев доступен всем авторизованным пользователям.
     */
    public function viewAny(?User $user): bool
    {
        return $user !== null;
    }

    public function view(?User $user, Comment $comment): bool
    {
        return $user !== null;
    }

    /**
     * Добавлять комментарии может любой авторизованный пользователь (читатель или модератор).
     */
    public function create(User $user): Response
    {
        return Response::allow();
    }

    public function update(User $user, Comment $comment): Response
    {
        if ($comment->user_id !== null && $comment->user_id === $user->id) {
            return Response::allow();
        }

        return Response::deny('Редактировать комментарии может только автор комментария и модератор.');
    }

    public function delete(User $user, Comment $comment): Response
    {
        if ($comment->user_id !== null && $comment->user_id === $user->id) {
            return Response::allow();
        }

        return Response::deny('Удалять комментарии может только автор комментария и модератор.');
    }
}
