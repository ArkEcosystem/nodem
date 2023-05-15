<?php

declare(strict_types=1);

namespace App\Services\Client\Resources;

final class Watcher extends AbstractResource
{
    public function getEvents(): array
    {
        return $this->client->send('watcher.getEvents');
    }
}
