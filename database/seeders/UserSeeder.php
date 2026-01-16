<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $moderatorRoleId = Role::query()->where('slug', 'moderator')->value('id');
        $readerRoleId = Role::query()->where('slug', 'reader')->value('id');

        // Модератор (для проверки работы ролей)
        User::query()->updateOrCreate(
            ['email' => 'moderator@example.com'],
            [
                'name' => 'Moderator',
                'password' => Hash::make('password'),
                'role_id' => $moderatorRoleId,
                'email_verified_at' => now(),
            ]
        );

        // Один читатель для тестов
        User::query()->updateOrCreate(
            ['email' => 'reader@example.com'],
            [
                'name' => 'Reader',
                'password' => Hash::make('password'),
                'role_id' => $readerRoleId,
                'email_verified_at' => now(),
            ]
        );
    }
}
