<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AboutProjectDepartmentsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_users_are_shown_in_departments_according_to_their_roles(): void
    {
        $editorRole = Role::where('name', 'Редактор')->firstOrFail();
        $moderatorRole = Role::where('name', 'Модератор')->firstOrFail();

        $editor = User::create([
            'username' => 'department_editor',
            'nickname' => 'Department Editor',
            'password' => 'temporary-password',
            'role_id' => $editorRole->id,
        ]);

        $moderator = User::create([
            'username' => 'department_moderator',
            'nickname' => 'Department Moderator',
            'password' => 'temporary-password',
            'role_id' => $moderatorRole->id,
        ]);

        $this->get(route('about-project'))
            ->assertOk()
            ->assertViewHas('editors', fn ($users) => $users->contains($editor))
            ->assertViewHas('moderators', fn ($users) => $users->contains($moderator))
            ->assertSeeText($editor->nickname)
            ->assertSeeText($moderator->nickname);
    }
}
