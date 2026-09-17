<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class TestAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $nicknames = [
            1 => 'Обычный пользователь',
            2 => 'Модератор',
            3 => 'Редактор',
            4 => 'Разработчик',
        ];

        foreach ($nicknames as $roleId => $nickname) {
            $tag = "dev0{$roleId}";

            if (User::where('username', $tag)->exists()) {
                continue;
            }

            User::create([
                'username' => $tag,
                'nickname' => $nickname,
                'password' => '12345678',
                'role_id' => $roleId,
            ]);
        }
    }
}