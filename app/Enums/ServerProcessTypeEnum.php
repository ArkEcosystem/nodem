<?php

declare(strict_types=1);

namespace App\Enums;

final class ServerProcessTypeEnum
{
    public const SEPARATE = 'separate';

    public const COMBINED = 'combined';

    public static function toArray(): array
    {
        return [
            static::SEPARATE,
            static::COMBINED,
        ];
    }
}
