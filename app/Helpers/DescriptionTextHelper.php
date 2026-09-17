<?php

namespace App\Helpers;

class DescriptionTextHelper
{
    /**
     * Приводит описание к plain-text:
     * - \n\n — разделитель абзацев,
     * - одиночный \n — мягкий перенос строки.
     *
     * Идемпотентен: повторный прогон уже нормализованного текста ничего не меняет.
     * Нужен как страховка на время, пока старые HTML-данные ещё не мигрированы.
     */
    public static function normalize(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $text = str_replace(["\r\n", "\r"], "\n", $value);
        $text = preg_replace('/<\s*\/?\s*br\s*\/?>/i', "\n", $text) ?? $text;
        $text = preg_replace('/[ \t]+\n/', "\n", $text) ?? $text;
        $text = preg_replace('/\n{2,}/', "\n\n", $text) ?? $text;

        return trim($text);
    }
}
