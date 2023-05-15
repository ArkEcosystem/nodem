<?php

declare(strict_types=1);

namespace App\Services\Client\Resources;

final class Info extends AbstractResource
{
    public function coreVersion(): array
    {
        return $this->client->send('info.coreVersion');
    }

    public function coreStatus(): array
    {
        return $this->client->send('info.coreStatus');
    }

    public function blockchainHeight(): array
    {
        return $this->client->send('info.blockchainHeight');
    }

    public function resources(): array
    {
        return $this->client->send('info.resources');
    }

    public function databaseSize(): array
    {
        return $this->client->send('info.databaseSize');
    }

    public function nextForgingSlot(): array
    {
        return $this->client->send('info.nextForgingSlot');
    }

    public function lastForgedBlock(): array
    {
        return $this->client->send('info.lastForgedBlock');
    }

    public function currentDelegate(): array
    {
        return $this->client->send('info.currentDelegate');
    }

    public function coreUpdate(bool $needsRestartProcesses = false): array
    {
        return $this->client->send('info.coreUpdate', [
            'restart' => $needsRestartProcesses,
        ]);
    }
}
