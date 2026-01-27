<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Защита от повторного запуска/ручных правок схемы
        if (Schema::hasColumn('comments', 'user_id')) {
            return;
        }

        $driver = DB::getDriverName();

        Schema::table('comments', function (Blueprint $table) use ($driver) {
            // Автор комментария (пользователь)
            // SQLite не умеет корректно добавлять внешний ключ через ALTER TABLE,
            // поэтому для sqlite добавляем только колонку (без FK-constraint).
            if ($driver === 'sqlite') {
                $table->unsignedBigInteger('user_id')
                    ->nullable()
                    ->after('article_id');
            } else {
                $table->foreignId('user_id')
                    ->nullable()
                    ->after('article_id')
                    ->constrained('users')
                    ->cascadeOnDelete();
            }

            $table->index(['user_id', 'created_at'], 'comments_user_id_created_at_index');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('comments', 'user_id')) {
            return;
        }

        $driver = DB::getDriverName();

        Schema::table('comments', function (Blueprint $table) use ($driver) {
            $table->dropIndex('comments_user_id_created_at_index');

            if ($driver === 'sqlite') {
                $table->dropColumn('user_id');
            } else {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};
