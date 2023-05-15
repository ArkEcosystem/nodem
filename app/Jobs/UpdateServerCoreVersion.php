<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\ServerUpdatingTasksEnum;
use App\Jobs\Concerns\ManagesFailedTask;
use App\Services\Client\RPC;
use Illuminate\Support\Arr;

final class UpdateServerCoreVersion extends HandlesLoadingState
{
    use ManagesFailedTask;

    protected function execute(): void
    {
        $response = RPC::fromServer($this->server)->info()->coreVersion();

        $this->server->refresh();

        $this->server->update([
            'core_version_current' => $response['currentVersion'] ?? $response['installedVersion'],
            'core_version_latest'  => $response['latestVersion'],
        ]);

        $this->server->fillMetaAttributes([
            'core_manager_current_version' => Arr::get($response, 'manager.currentVersion'),
            'core_manager_latest_version'  => Arr::get($response, 'manager.latestVersion'),
        ]);
    }

    protected function getTaskName(): string
    {
        return ServerUpdatingTasksEnum::UPDATING_SERVER_CORE;
    }
}
