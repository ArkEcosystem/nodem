<?php

declare(strict_types=1);

namespace App\Enums;

final class ProcessStatusEnum
{
    public const UNDEFINED = 'undefined';

    public const ONLINE = 'online';

    public const STOPPED = 'stopped';

    public const STOPPING = 'stopping';

    public const WAITING_RESTART = 'waiting restart';

    public const LAUNCHING = 'launching';

    public const ERRORED = 'errored';

    public const ONE_LAUNCH_STATUS = 'one-launch-status';

    public const DELETED = 'deleted';

    public static function isUndefined(string $value):bool
    {
        return $value === static::UNDEFINED;
    }

    public static function isOnline(string $value):bool
    {
        return $value === static::ONLINE;
    }

    public static function isStopped(string $value):bool
    {
        return $value === static::STOPPED;
    }

    public static function isStopping(string $value):bool
    {
        return $value === static::STOPPING;
    }

    public static function isWaitingRestart(string $value):bool
    {
        return $value === static::WAITING_RESTART;
    }

    public static function isLaunching(string $value):bool
    {
        return $value === static::LAUNCHING;
    }

    public static function isErrored(string $value):bool
    {
        return $value === static::ERRORED;
    }

    public static function isOneLaunchStatus(string $value):bool
    {
        return $value === static::ONE_LAUNCH_STATUS;
    }

    public static function isDeleted(string $value): bool
    {
        return $value === static::DELETED;
    }
}
