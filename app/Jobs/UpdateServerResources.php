<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\ServerUpdatingTasksEnum;
use App\Jobs\Concerns\ManagesFailedTask;
use App\Services\Client\RPC;

final class UpdateServerResources extends HandlesLoadingState
{
    use ManagesFailedTask;

    protected function execute(): void
    {
        $response = RPC::fromServer($this->server)->info()->resources();

        $this->server->update([
            'cpu_total'      => $response['cpu']['total'],
            'cpu_used'       => $response['cpu']['used'],
            'cpu_available'  => $response['cpu']['available'],
            'ram_total'      => $response['ram']['total'],
            'ram_used'       => $response['ram']['used'],
            'ram_available'  => $response['ram']['available'],
            'disk_total'     => $response['disk']['total'],
            'disk_used'      => $response['disk']['used'],
            'disk_available' => $response['disk']['available'],
        ]);

        $this->server->resourceIndicators()->create([
            'cpu'  => $response['cpu']['used'],
            'ram'  => $response['ram']['used'],
            'disk' => $response['disk']['used'],
        ]);
    }

    protected function getTaskName(): string
    {
        return ServerUpdatingTasksEnum::UPDATING_SERVER_RESOURCES;
    }
}
