<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::query()->updateOrCreate(
            ['slug' => 'moderator'],
            ['name' => 'Модератор']
        );

        Role::query()->updateOrCreate(
            ['slug' => 'reader'],
            ['name' => 'Читатель']
        );
    }
}
