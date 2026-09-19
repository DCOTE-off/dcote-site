<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\User;
use App\Policies\CommentPolicy;
use Tests\TestCase;

class CommentPolicyTest extends TestCase
{
    private CommentPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new CommentPolicy;
    }

    public function test_author_can_update_and_delete_own_comment(): void
    {
        $author = $this->userWithRole(1, 10);
        $comment = $this->commentOwnedBy(10);

        $this->assertTrue($this->policy->update($author, $comment));
        $this->assertTrue($this->policy->delete($author, $comment));
    }

    public function test_non_author_cannot_update_or_delete(): void
    {
        $stranger = $this->userWithRole(1, 20);
        $comment = $this->commentOwnedBy(10);

        $this->assertFalse($this->policy->update($stranger, $comment));
        $this->assertFalse($this->policy->delete($stranger, $comment));
    }

    public function test_moderation_roles_can_delete_others_comments(): void
    {
        $comment = $this->commentOwnedBy(10);

        foreach ([2, 3, 4] as $roleId) {
            $moderator = $this->userWithRole($roleId, 30 + $roleId);

            $this->assertTrue($this->policy->delete($moderator, $comment));
            $this->assertFalse($this->policy->update($moderator, $comment));
        }
    }

    private function userWithRole(int $roleId, int $id): User
    {
        $user = new User(['role_id' => $roleId]);
        $user->id = $id;

        return $user;
    }

    private function commentOwnedBy(int $userId): Comment
    {
        return new Comment(['user_id' => $userId]);
    }
}
