<?php

function format_date(string $utcDate, $timezone = 'Europe/Moscow') {
    if (empty($utcDate)) {
        return '';
    }
    $date = new DateTime($utcDate, new DateTimeZone('UTC'));
    $date->setTimezone(new DateTimeZone($timezone));
    return $date->format('d.m.Y');
}