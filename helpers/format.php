<?php

function sr_date($date): string
{
    if (
        empty($date)
        ||
        $date === '0000-00-00'
    ) {
        return '-';
    }

    return date(
        'd.m.Y.',
        strtotime($date)
    );
}

function sr_time($date): string
{
    if (empty($date)) {
        return '-';
    }

    return date(
        'H:i',
        strtotime($date)
    );
}

function sr_datetime($date): string
{
    if (empty($date)) {
        return '-';
    }

    return date(
        'd.m.Y. H:i',
        strtotime($date)
    );
}

function sr_duration_minutes($minutes): string
{
    $minutes = (int)$minutes;

    $hours =
        floor($minutes / 60);

    $remaining =
        $minutes % 60;

    return
        $hours . 'h '
        . $remaining . 'm';
}

function db_date(?string $value): ?string
{
    if (empty($value)) {
        return null;
    }

    $date = DateTime::createFromFormat(
        'd.m.Y',
        trim($value)
    );

    return $date
        ? $date->format('Y-m-d')
        : null;
}

function db_datetime(?string $value): ?string
{
    if (empty($value)) {
        return null;
    }

    $date = DateTime::createFromFormat(
        'd.m.Y H:i',
        trim($value)
    );

    return $date
        ? $date->format('Y-m-d H:i:s')
        : null;
}
