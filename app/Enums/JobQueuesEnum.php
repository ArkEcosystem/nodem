<?php

declare(strict_types=1);

namespace App\Enums;

/* @TODO: transform to Enum when move to php8.1 */
final class JobQueuesEnum
{
    public const DEFAULT_QUEUE = 'default';

    public const BACKGROUND_UPDATES_QUEUE = 'background-updates';
}
