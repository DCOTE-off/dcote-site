<?php

namespace App\Helpers;

use Illuminate\Support\HtmlString;

class SafeDescriptionHelper
{
    private const ALLOWED_TAGS = '<br><p><b><strong><i><em><ul><ol><li>';

    public static function render(?string $value, string $fallback): HtmlString
    {
        $content = $value !== null && trim($value) !== '' ? $value : $fallback;
        $content = strip_tags($content, self::ALLOWED_TAGS);
        $content = preg_replace_callback(
            '/<\s*(\/?)\s*(br|p|b|strong|i|em|ul|ol|li)\b[^>]*>/iu',
            static fn (array $tag): string => '<'.$tag[1].strtolower($tag[2]).'>',
            $content,
        );

        return new HtmlString($content ?? '');
    }
}
