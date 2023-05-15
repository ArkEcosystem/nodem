<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Spatie\ResponseCache\Facades\ResponseCache;

trait ClearsResponseCache
{
    final public static function bootClearsResponseCache(): void
    {
        self::created(function () {
            ResponseCache::clear();
        });

        self::updated(function () {
            ResponseCache::clear();
        });

        self::deleted(function () {
            ResponseCache::clear();
        });
    }
}
