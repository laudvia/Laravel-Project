<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();

            // Заголовок новости
            $table->string('title');

            // Короткое описание (можно показывать в списке)
            $table->text('excerpt')->nullable();

            // Полный текст
            $table->longText('content');

            // Дата публикации (nullable, чтобы можно было делать черновики)
            $table->timestamp('published_at')->nullable()->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
