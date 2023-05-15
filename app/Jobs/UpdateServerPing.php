<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\ServerUpdatingTasksEnum;
use App\Exceptions\UnreachableServer;
use App\Jobs\Concerns\ManagesFailedTask;
use App\Services\Client\Client;

final class UpdateServerPing extends HandlesLoadingState
{
    use ManagesFailedTask;

    protected function execute(): void
    {
        $ping = Client::ping($this->server->host);

        if ($ping === false) {
            throw new UnreachableServer();
        }

        $this->server->refresh()->update([
            'ping' => $ping,
        ]);
    }

    protected function getTaskName(): string
    {
        return ServerUpdatingTasksEnum::UPDATING_SERVER_PING;
    }
}
