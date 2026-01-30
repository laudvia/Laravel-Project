## Лабораторная работа №11: Широковещание (Pusher + Laravel Echo)

Реализовано:
- Событие `App\Events\NewArticleEvent` implements `ShouldBroadcast`.
  - Публичный канал: `test` (используется `Channel`, не `PrivateChannel`).
  - Payload через `broadcastWith()` → `{ article: { id, title, excerpt, published_at } }`.
- В `ArticleController@store` после создания статьи вызывается `event(new NewArticleEvent($article));`.
- Подключен Laravel Echo в `resources/js/bootstrap.js`.
- Добавлен Vue-компонент `resources/js/components/App.vue`, который слушает:
  - `Echo.channel('test').listen('NewArticleEvent', ...)` и показывает alert + простое модальное окно.
- В `resources/views/layout.blade.php` подключён `@vite(['resources/css/app.css','resources/js/app.js'])` и добавлен `<div id="app"></div>`.

Что нужно настроить у себя:
1) Создать приложение в Pusher и скопировать ключи в `.env`:
   - `PUSHER_APP_ID`, `PUSHER_APP_KEY`, `PUSHER_APP_SECRET`, `PUSHER_APP_CLUSTER=eu`
   - `BROADCAST_DRIVER=pusher`
2) Установить зависимости:
   - `composer install` (или `composer update` чтобы подтянуть `pusher/pusher-php-server`)
   - `npm install`
3) Запустить:
   - очередь: `php artisan queue:work`
   - фронтенд: `npm run dev` (или `npm run watch`)
   - сервер: `php artisan serve`


Посмотреть все роли:
php artisan tinker

\App\Models\Role::all();

Красиво (только нужные поля):
\App\Models\Role::query()->select('id','name','slug')->get()->toArray();

Посмотреть пользователей с ролями:
\App\Models\User::with('role')->select('id','email','role_id')->get()->toArray();


Терминал 1 — Laravel
php artisan serve

Терминал 2 — очередь
php artisan queue:work

Терминал 3 — Vite
npm run dev