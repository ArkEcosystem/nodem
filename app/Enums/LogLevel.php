<?php

declare(strict_types=1);

namespace App\Enums;

final class LogLevel
{
    public const DEBUG = 'debug';

    public const FATAL = 'fatal';

    public const INFO = 'info';

    public const ERROR = 'error';

    public const WARNING = 'warning';

    public const TRACE = 'trace';

    public const NOTICE = 'notice';

    public static function all() : array
    {
        return [
            static::FATAL,
            static::ERROR,
            static::WARNING,
            static::INFO,
            static::DEBUG,
            static::TRACE,
            static::NOTICE,
        ];
    }

    public static function withLabels() : array
    {
        return collect(static::all())->mapWithKeys(fn ($key) => [
            $key => trans('status.'.$key),
        ])->toArray();
    }
}
