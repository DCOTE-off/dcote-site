<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            1 => 'Обычный пользователь',
            2 => 'Модератор',
            3 => 'Редактор',
            4 => 'Разработчик',
        ];

        foreach ($roles as $id => $name) {
            DB::table('roles')->insertOrIgnore([
                'id' => $id,
                'name' => $name,
                'description' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
