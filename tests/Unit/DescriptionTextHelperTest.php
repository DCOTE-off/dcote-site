<?php

namespace Tests\Unit;

use App\Helpers\DescriptionTextHelper;
use PHPUnit\Framework\TestCase;

class DescriptionTextHelperTest extends TestCase
{
    public function test_it_converts_br_tags_and_normalizes_newlines(): void
    {
        $result = DescriptionTextHelper::normalize("Первая строка<br>вторая<br/>\n\nНовый абзац");

        $this->assertSame("Первая строка\nвторая\n\nНовый абзац", $result);
    }

    public function test_it_is_idempotent(): void
    {
        $once = DescriptionTextHelper::normalize('a<br>b<br><br>c');

        $this->assertSame("a\nb\n\nc", $once);
        $this->assertSame($once, DescriptionTextHelper::normalize($once));
    }

    public function test_null_becomes_empty_string(): void
    {
        $this->assertSame('', DescriptionTextHelper::normalize(null));
    }
}
