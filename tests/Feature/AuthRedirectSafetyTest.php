<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthRedirectSafetyTest extends TestCase
{
    public function test_external_referer_is_not_saved_as_an_intended_url(): void
    {
        $this->withHeader('referer', 'https://attacker.example/phishing')
            ->get('/auth/login')
            ->assertOk()
            ->assertSessionMissing('url.intended');
    }

    public function test_same_origin_referer_is_saved_as_a_relative_url(): void
    {
        $this->withHeader('referer', url('/anime/1?tab=episodes'))
            ->get('/auth/login')
            ->assertOk()
            ->assertSessionHas('url.intended', '/anime/1?tab=episodes');
    }
}
