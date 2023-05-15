<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns;

use App\Facades\CacheStore;
use Closure;
use Spatie\Backtrace\Backtrace;

trait CanBeCached
{
    private function storeWithCache(Closure $callback, array $tags = []): mixed
    {
        return CacheStore::hour(sprintf(
            '%s:%s:%s',
            Backtrace::create()->limit(2)->frames()[1]->class,
            Backtrace::create()->limit(2)->frames()[1]->method,
            $this->model->id,
        ), $callback, $tags);
    }
}
