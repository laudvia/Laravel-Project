<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArticlePolicy
{
    /**
     * Просмотр списка новостей доступен всем (включая гостей)
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Просмотр отдельной новости доступен всем (включая гостей)
     */
    public function view(?User $user, Article $article): bool
    {
        return true;
    }

    /**
     * Создание доступно только модератору
     */
    public function create(User $user): Response
    {
        return Response::deny('Создавать новости может только модератор.');
    }

    /**
     * Редактирование доступно только модератору
     */
    public function update(User $user, Article $article): Response
    {
        return Response::deny('Редактировать новости может только модератор.');
    }

    /**
     * Удаление доступно только модератору
     */
    public function delete(User $user, Article $article): Response
    {
        return Response::deny('Удалять новости может только модератор.');
    }
}
