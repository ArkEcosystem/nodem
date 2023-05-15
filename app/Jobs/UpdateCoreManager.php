<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\ServerUpdatingTasksEnum;
use App\Jobs\Concerns\ManagesFailedTask;
use App\Services\Client\RPC;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;

final class UpdateCoreManager extends HandlesLoadingState
{
    use ManagesFailedTask;

    public int $timeout = 60;

    protected function execute(): void
    {
        RPC::fromServer($this->server)->plugin()->update();

        Bus::chain([
            new RestartCoreManager($this->server, $this->initiator),
            (new UpdateServerCoreVersion($this->server))->delay(Carbon::now()->addSeconds(5)),
        ])->dispatch();
    }

    protected function getTaskName(): string
    {
        return ServerUpdatingTasksEnum::UPDATING_SERVER_CORE_MANAGER;
    }
}
