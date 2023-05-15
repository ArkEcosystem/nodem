<?php

declare(strict_types=1);

namespace App\Services\Client\Resources;

final class Snapshot extends AbstractResource
{
    public function list(): array
    {
        return $this->client->send('snapshots.list');
    }

    public function delete(): array
    {
        return $this->client->send('snapshots.delete');
    }

    public function create(): array
    {
        return $this->client->send('snapshots.create');
    }

    public function restore(): array
    {
        return $this->client->send('snapshots.restore');
    }
}
