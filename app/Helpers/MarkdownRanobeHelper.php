<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MarkdownRanobeHelper
{
    public static function parse(string $markdown, int $year, float $volume)
    {
        $volumeStr = floatval($volume);
        $folderPath = "ranobe/year-{$year}/volume-{$volumeStr}/images";
        $imagesBaseUrl = Storage::disk('public')->url($folderPath);

        $markdown = preg_replace(
            '/!\[\[(.*?)\]\]/i',
            '![иллюстрация]('.$imagesBaseUrl.'/$1)',
            $markdown
        );

        $markdown = preg_replace(
            '/(!\[.*?\]\()(?!https?:\/\/)(.*?\))/i',
            '$1'.$imagesBaseUrl.'/$2',
            $markdown
        );

        $markdown = preg_replace_callback('/^\s*((\d+)\.|[\*\-])(\s+)/m', function ($matches) {
            $marker = trim($matches[1]);
            $space = $matches[3];

            if (str_ends_with($marker, '.')) {
                $num = rtrim($marker, '.');
                $escaped = $num.'\.'.$space;
            } else {
                $escaped = '\\'.$marker.$space;
            }

            return "\n\n".$escaped;
        }, $markdown);

        $html = Str::markdown($markdown);
        $html = preg_replace('/(?<!\.)\.(\s*<\/h3>)/i', '$1', $html);

        $html = preg_replace('/<hr\s*\/?>/i', '<p class="custom-hr">***</p>', $html);

        $html = preg_replace('/<p>\s*<\/p>/i', '', $html);
        $html = preg_replace('/<img/i', '<img loading="lazy" decoding="async"', $html);
        $html = str_replace(['<h2>', '</h2>'], ['<h3>', '</h3>'], $html);

        return $html;
    }
}
