## Лабораторная работа №13: КЭШ



Терминал 1 — Laravel
php artisan serve

Терминал 2 — очередь
php artisan queue:work

Терминал 3 — Vite
npm run dev

---


Проверка выполнения лабораторной (обязательные команды)
1) Показать, что кеш пишется в БД

Команда должна вывести DB_CACHE_OK:

php artisan tinker --execute='$s=cache()->store("database"); $before=DB::table("cache")->count(); $s->put("lr13_demo_".time(),"ok",60); $after=DB::table("cache")->count(); echo (($after>$before) ? "DB_CACHE_OK" : "DB_CACHE_FAIL").PHP_EOL;'

2) Показать, что flush реально чистит

Команда должна показать, что после Cache::flush() записей стало 0:

php artisan tinker --execute='cache()->store("database")->put("proof","1",600); echo "before=".DB::table("cache")->count().PHP_EOL; Cache::flush(); echo "after=".DB::table("cache")->count().PHP_EOL;'
