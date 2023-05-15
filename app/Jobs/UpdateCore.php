<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\ServerUpdatingTasksEnum;
use App\Jobs\Concerns\ManagesFailedTask;
use App\Services\Client\RPC;

final class UpdateCore extends HandlesLoadingState
{
    use ManagesFailedTask;

    protected function execute(): void
    {
        RPC::fromServer($this->server)->info()->coreUpdate(needsRestartProcesses: true);

        UpdateServerCoreVersion::dispatch($this->server, $this->initiator);
    }

    protected function getTaskName(): string
    {
        return ServerUpdatingTasksEnum::UPDATING_SERVER_CORE;
    }
}
