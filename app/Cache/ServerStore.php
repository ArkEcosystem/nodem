<?php

declare(strict_types=1);

namespace App\Cache;

use App\Models\Server;
use Illuminate\Support\Facades\Cache;

final class ServerStore
{
    public static function flush(Server $server): void
    {
        Cache::tags([
            static::getViewCacheTag($server),
        ])->flush();
    }

    public static function getViewCacheTag(Server $server): string
    {
        return sprintf('server.%s', $server->id);
    }
}
