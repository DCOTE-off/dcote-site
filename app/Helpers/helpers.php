<?php
use Illuminate\Support\Carbon;

function format_date(string $utcDate, $timezone = 'Europe/Moscow') {
    if (empty($utcDate)) {
        return '';
    }
    $date = new DateTime($utcDate, new DateTimeZone('UTC'));
    $date->setTimezone(new DateTimeZone($timezone));
    return $date->format('d.m.Y');
}

function RussianDate(string $date_numbers): string
{
    if (!$date_numbers) {
        return '';
    }
    return Carbon::parse($date_numbers)
        ->locale('ru')
        ->isoFormat('D MMMM Y года');
}