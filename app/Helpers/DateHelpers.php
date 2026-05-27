<?php

use Illuminate\Support\Carbon;

if (!function_exists('format_date')) {
    function format_date(string $utcDate, $timezone = 'Europe/Moscow') {
        if (empty($utcDate)) {
            return '';
        }
        $date = new DateTime($utcDate, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone($timezone));
        return $date->format('d.m.Y');
    }
}

if (!function_exists('russian_date')) {
    function russian_date(string $date_numbers): string {
        if (!$date_numbers) {
            return '';
        }
        return Carbon::parse($date_numbers)
            ->locale('ru')
            ->isoFormat('D MMMM Y года');
    }
}