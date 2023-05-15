<?php

declare(strict_types=1);

namespace App\Enums;

final class ServerTypeEnum
{
    public const CORE = 'core';

    public const CORE_MANAGER = 'manager';

    public const RELAY = 'relay';

    public const FORGER = 'forger';

    public static function isCore(string $value):bool
    {
        return $value === static::CORE;
    }

    public static function isCoreManager(string $value):bool
    {
        return $value === static::CORE_MANAGER;
    }

    public static function isRelay(string $value):bool
    {
        return $value === static::RELAY;
    }

    public static function isForger(string $value):bool
    {
        return $value === static::FORGER;
    }

    public static function toArray(): array
    {
        return [
            static::CORE,
            static::CORE_MANAGER,
            static::RELAY,
            static::FORGER,
        ];
    }
}
