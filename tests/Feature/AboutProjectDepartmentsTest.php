<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutProjectDepartmentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_are_shown_in_departments_according_to_their_roles(): void
    {
        $editorRole = Role::create(['name' => 'Редактор', 'description' => 'Тестовая роль']);
        $moderatorRole = Role::create(['name' => 'Модератор', 'description' => 'Тестовая роль']);

        User::create([
            'username' => 'department_editor',
            'nickname' => 'Department Editor',
            'password' => 'temporary-password',
            'role_id' => $editorRole->id,
        ]);

        User::create([
            'username' => 'department_moderator',
            'nickname' => 'Department Moderator',
            'password' => 'temporary-password',
            'role_id' => $moderatorRole->id,
        ]);

        $props = $this->get(route('about-project'))
            ->assertOk()
            ->viewData('page')['props'];

        $this->assertTrue(
            collect($props['editors'])->pluck('nickname')->contains('Department Editor')
        );
        $this->assertTrue(
            collect($props['moderators'])->pluck('nickname')->contains('Department Moderator')
        );
    }
}
