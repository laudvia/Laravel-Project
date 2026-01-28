## Лабораторная работа №9: Роли пользователей + Policies + Gate (Laravel)

Реализовано:
- Таблица ролей `roles` (migration) + модель `Role`.
- Связь **User belongsTo Role**, **Role hasMany Users** (Eloquent relationship).
- Добавлено поле `role_id` в `users` (migration).
- Seeder:
  - `RoleSeeder` заполняет роли: `moderator`, `reader`.
  - `UserSeeder` создаёт тестовых пользователей (модератор и читатель).
  - `DatabaseSeeder` запускает эти сидеры и создаёт тестовые новости и комментарии.
- Auth:
  - При регистрации новый пользователь получает роль **читатель**.
- Gate/Policies:
  - `Gate::before` в `AuthServiceProvider` пропускает модератора **прежде остальных** проверок.
  - `ArticlePolicy`: любые CRUD действия только модератор.
  - `CommentPolicy`: создавать комментарии может любой авторизованный, редактировать/удалять — только модератор.
- Blade:
  - Вкладка **«Создать новость»** в навигации доступна только модератору.
  - Кнопки редактирования/удаления для статей и комментариев видны только модератору.
- Кастомные ответы:
  - 403-страница (`resources/views/errors/403.blade.php`) и обработка `AuthorizationException`.
  - При отсутствии авторизации — редирект на логин с сообщением.

Тестовые пользователи (после `php artisan migrate:fresh --seed`):
- **moderator@example.com** / **password**
- **reader@example.com** / **password**

Запуск:
```bash
composer install
cp .env.example .env
php artisan key:generate

php artisan migrate:fresh --seed
php artisan serve
```

Примечание: после загрузки проекта на GitHub укажите ссылку на ваш репозиторий в ответе в СДО.

---

Посмотреть все роли:
php artisan tinker

\App\Models\Role::all();

Красиво (только нужные поля):
\App\Models\Role::query()->select('id','name','slug')->get()->toArray();

Посмотреть пользователей с ролями:
\App\Models\User::with('role')->select('id','email','role_id')->get()->toArray();