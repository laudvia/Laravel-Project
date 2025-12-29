<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Модель новости (Article).
 *
 * Учебная цель: показать работу с моделями + фабриками/сидерами.
 */
class Article extends Model
{
    use HasFactory;

    /**
     * Разрешаем массовое заполнение (mass assignment) для учебного примера.
     */
    protected $fillable = [
        'title',
        'excerpt',
        'content',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
