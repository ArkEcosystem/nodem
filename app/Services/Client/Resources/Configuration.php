<?php

declare(strict_types=1);

namespace App\Services\Client\Resources;

final class Configuration extends AbstractResource
{
    public function getEnv(): array
    {
        return $this->client->send('configuration.getEnv');
    }

    public function setEnv(array $params): array
    {
        return $this->client->send('configuration.setEnv', $params);
    }

    public function getPlugins(): array
    {
        return $this->client->send('configuration.getPlugins');
    }

    public function setPlugins(array $params): array
    {
        return $this->client->send('configuration.setPlugins', $params);
    }
}
