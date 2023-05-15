<?php

declare(strict_types=1);

namespace App\Services\Client\Resources;

use App\Services\Client\Client;

abstract class AbstractResource
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}
