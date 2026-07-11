<?php

namespace Tests\Unit;

use App\Helpers\SafeDescriptionHelper;
use PHPUnit\Framework\TestCase;

class SafeDescriptionHelperTest extends TestCase
{
    public function test_safe_formatting_is_preserved_without_attributes(): void
    {
        $result = (string) SafeDescriptionHelper::render(
            '<p onclick="alert(1)">Текст<br class="x"><strong style="color:red">важно</strong></p>',
            'fallback',
        );

        $this->assertSame('<p>Текст<br><strong>важно</strong></p>', $result);
    }

    public function test_executable_markup_is_removed(): void
    {
        $result = (string) SafeDescriptionHelper::render(
            '<script>alert(1)</script><img src=x onerror=alert(2)>Описание',
            'fallback',
        );

        $this->assertStringNotContainsString('<script', $result);
        $this->assertStringNotContainsString('<img', $result);
        $this->assertStringNotContainsString('onerror', $result);
    }
}
