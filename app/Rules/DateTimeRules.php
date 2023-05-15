<?php

declare(strict_types=1);

namespace App\Rules;

final class DateTimeRules
{
    public static function nullable(string $dateField, string $timeField) : array
    {
        return [
            $dateField => ['nullable', 'string', 'date_format:d.m.Y'],
            $timeField => ['nullable', 'required_with:'.$dateField, 'string', 'date_format:H:i:s'],
        ];
    }

    public static function required(string $dateField, string $timeField) : array
    {
        return [
            $dateField => ['required', 'string', 'date_format:d.m.Y'],
            $timeField => ['required', 'string', 'date_format:H:i:s'],
        ];
    }
}
