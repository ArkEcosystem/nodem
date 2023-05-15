<?php

declare(strict_types=1);

namespace App\Services\Client;

use App\Models\Server;
use App\Services\Client\Resources\Configuration;
use App\Services\Client\Resources\Info;
use App\Services\Client\Resources\Log;
use App\Services\Client\Resources\Plugin;
use App\Services\Client\Resources\Process;
use App\Services\Client\Resources\Snapshot;
use App\Services\Client\Resources\Watcher;

final class RPC
{
    private Client $client;

    private function __construct(Client $client)
    {
        $this->client = $client;
    }

    public static function fromServer(Server $server): self
    {
        return new static(Client::fromServer($server));
    }

    public function log(): Log
    {
        return new Log($this->client);
    }

    public function configuration(): Configuration
    {
        return new Configuration($this->client);
    }

    public function info(): Info
    {
        return new Info($this->client);
    }

    public function process(): Process
    {
        return new Process($this->client);
    }

    public function snapshot(): Snapshot
    {
        return new Snapshot($this->client);
    }

    public function watcher(): Watcher
    {
        return new Watcher($this->client);
    }

    public function plugin(): Plugin
    {
        return new Plugin($this->client);
    }
}
