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

        // 1. Изображения Obsidian
        $markdown = preg_replace(
            '/!\[\[(.*?)\]\]/i',
            '![иллюстрация](' . $imagesBaseUrl . '/$1)',
            $markdown
        );

        // 2. Изображения Classic
        $markdown = preg_replace(
            '/(!\[.*?\]\()(?!https?:\/\/)(.*?\))/i',
            "$1" . $imagesBaseUrl . "/$2",
            $markdown
        );

        // 3. МАГИЯ ЗДЕСЬ: "Убиваем" списки до парсинга!
        // Ищем начало строки (даже если там табы/пробелы), за которыми идет "1." или "*" или "-"
        $markdown = preg_replace_callback('/^\s*((\d+)\.|[\*\-])(\s+)/m', function($matches) {
            $marker = trim($matches[1]); // Достаем саму метку ("1." или "*")
            $space = $matches[3];        // Достаем пробел после метки
            
            if (str_ends_with($marker, '.')) {
                $num = rtrim($marker, '.');
                $escaped = $num . '\.' . $space; // Превращаем "1. " в "1\. "
            } else {
                $escaped = '\\' . $marker . $space; // Превращаем "* " в "\* "
            }
            
            // Добавляем ПЕРЕД строкой два переноса строки (\n\n).
            // Это гарантирует, что Markdown не слепит их в одну кучу, 
            // а послушно обернет каждый пункт в отдельный тег <p>
            return "\n\n" . $escaped;
        }, $markdown);

        // 4. Парсим Markdown в HTML.
        // Теперь парсер видит просто текст и заворачивает всё в <p>, оставляя цифры и звездочки на месте.
        $html = Str::markdown($markdown);
        $html = preg_replace('/(?<!\.)\.(\s*<\/h3>)/i', '$1', $html);
        // 5. Обработка горизонтальных линий (---)
        $html = preg_replace('/<hr\s*\/?>/i', '<p class="custom-hr">***</p>', $html);

        // 6. Подчищаем пустые параграфы (на случай лишних переносов)
        $html = preg_replace('/<p>\s*<\/p>/i', '', $html);
        $html = preg_replace('/<img/i', '<img loading="lazy" decoding="async"', $html);

        return $html;
    }
}