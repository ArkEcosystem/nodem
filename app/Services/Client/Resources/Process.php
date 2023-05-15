<?php

declare(strict_types=1);

namespace App\Services\Client\Resources;

final class Process extends AbstractResource
{
    public function list(): array
    {
        return $this->client->send('process.list');
    }

    public function start(string $name, array $params = []): array
    {
        return $this->client->send(
            'process.start',
            array_merge(
                $params,
                ['name' => $name, 'args' => $params['args'] ?? '']
            )
        );
    }

    public function stop(string $name): array
    {
        return $this->client->send('process.stop', ['name' => $name]);
    }

    public function restart(string $name): array
    {
        return $this->client->send('process.restart', ['name' => $name]);
    }

    public function delete(string $name): array
    {
        return $this->client->send('process.delete', ['name' => $name]);
    }
}
