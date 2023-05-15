<?php

declare(strict_types=1);

namespace App\Services\Client\Resources;

final class Plugin extends AbstractResource
{
    public function update(): array
    {
        return $this->client->send('plugin.update', ['name' => '@arkecosystem/core-manager']);
    }
}
