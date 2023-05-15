<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\HasRoute;
use App\Models\Concerns\ClearsResponseCache;
use ARKEcosystem\Foundation\Hermes\Models\DatabaseNotification as Hermes;

/**
 * @property ?Server $relatable
 */
final class DatabaseNotification extends Hermes implements HasRoute
{
    use ClearsResponseCache;

    public function name(): string
    {
        return $this->relatable?->name ?? abort(404);
    }

    public function logo(): string
    {
        return url('/images/logo.svg');
    }

    public function route(): string|null
    {
        return $this->relatable?->route();
    }
}
