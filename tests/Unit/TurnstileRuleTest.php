<?php

namespace Tests\Unit;

use App\Rules\Turnstile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TurnstileRuleTest extends TestCase
{
    public function test_unsuccessful_turnstile_service_response_fails_validation(): void
    {
        config()->set('services.cloudflare.enabled', true);
        config()->set('services.cloudflare.secret', 'test-secret');
        Http::fake([
            'challenges.cloudflare.com/*' => Http::response([], 503),
        ]);

        $messages = [];
        (new Turnstile)->validate(
            'cf-turnstile-response',
            'test-response',
            function (string $message) use (&$messages): void {
                $messages[] = $message;
            },
        );

        $this->assertNotEmpty($messages);
    }

    public function test_rule_defines_short_connection_and_request_timeouts(): void
    {
        $source = file_get_contents(app_path('Rules/Turnstile.php'));

        $this->assertStringContainsString('->connectTimeout(2)', $source);
        $this->assertStringContainsString('->timeout(5)', $source);
    }
}
