# Laravel Project

## Лабораторная работа: Blade / Routes / Controller / Validate / CSRF / Paginate / CRUD

В проекте реализован модуль **Новостей** (модель `Article`) с полным CRUD:

- **Список новостей**: пагинация (`paginate(10)`) + кнопки *Просмотр / Редактировать / Удалить*.
- **Создание новости**: форма Blade, `@csrf`, валидация (`$request->validate`).
- **Редактирование новости**: форма Blade, `@csrf` + `@method('PUT')`, валидация.
- **Удаление новости**: форма Blade, `@csrf` + `@method('DELETE')`.
- **Просмотр новости**: отдельная страница.

Маршруты:
- `GET /articles` — список
- `GET /articles/create` — создание
- `POST /articles` — сохранение
- `GET /articles/{article}` — просмотр
- `GET /articles/{article}/edit` — редактирование
- `PUT /articles/{article}` — обновление
- `DELETE /articles/{article}` — удаление
- `GET /news` — редирект на `/articles`

## Запуск (SQLite — самый простой вариант)

```bash
composer install
cp .env.example .env
php artisan key:generate

# SQLite
touch database/database.sqlite
# В .env поставить:
# DB_CONNECTION=sqlite
# DB_DATABASE=/ABSOLUTE/PATH/TO/project/database/database.sqlite

php artisan optimize:clear
php artisan migrate --seed
php artisan serve
```

Открыть: `http://127.0.0.1:8000/articles`
