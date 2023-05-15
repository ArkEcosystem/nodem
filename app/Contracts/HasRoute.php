<?php

declare(strict_types=1);

namespace App\Contracts;

interface HasRoute
{
    public function route(): string|null;
}
