<?php

namespace App\Helpers;

class SeoMeta
{
    public const SITE_NAME = 'DCOTE';

    public const DEFAULT_DESCRIPTION = 'DCOTE - сайт, который совмещает в себе все аспекты произведения «Добро пожаловать в класс превосходства». Википедия, новости, аниме, ранобэ, манга и не только!';

    public const DEFAULT_IMAGE = 'images/og-main-preview.webp';

    /**
     * @param  array<string, mixed>  $overrides  дополнительные/переопределяющие ключи
     * @return array<string, mixed>
     */
    public static function make(
        string $title,
        ?string $description = null,
        ?string $image = null,
        array $overrides = [],
    ): array {
        $resolvedImage = self::imageUrl($image);
        $isDefaultImage = $resolvedImage === self::imageUrl(null);

        $meta = [
            'title' => self::formatTitle($title),
            'description' => $description ?: self::DEFAULT_DESCRIPTION,
            'image' => $resolvedImage,
            'type' => 'website',
            'robots' => 'index, follow',
        ];

        if ($isDefaultImage) {
            $meta['image_width'] = 1200;
            $meta['image_height'] = 630;
        }

        return array_merge($meta, $overrides);
    }

    public static function formatTitle(string $title): string
    {
        $title = trim($title);

        return $title === '' ? self::SITE_NAME : $title.' | '.self::SITE_NAME;
    }

    public static function imageUrl(?string $image): string
    {
        $image = $image ?: self::DEFAULT_IMAGE;

        return str_starts_with($image, 'http') ? $image : asset($image);
    }
}
