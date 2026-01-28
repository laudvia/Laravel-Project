<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Защита от повторного запуска/ручных правок схемы
        if (Schema::hasColumn('comments', 'is_approved')) {
            return;
        }

        Schema::table('comments', function (Blueprint $table) {
            // Подтверждение модерации (по умолчанию: ожидает)
            $table->boolean('is_approved')
                ->default(false)
                ->after('body');

            $table->index(['is_approved', 'created_at'], 'comments_is_approved_created_at_index');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('comments', 'is_approved')) {
            return;
        }

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex('comments_is_approved_created_at_index');
            $table->dropColumn('is_approved');
        });
    }
};
