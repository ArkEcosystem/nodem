<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\JobQueuesEnum;
use App\Jobs\CheckServerProcessStatus;
use App\Jobs\UpdateProcesses;
use App\Jobs\UpdateServerCoreVersion;
use App\Jobs\UpdateServerHeight;
use App\Jobs\UpdateServerPing;
use App\Jobs\UpdateServerResources;
use App\Models\Server;
use App\Models\User;
use Illuminate\Bus\Batch;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Throwable;

final class ServerDetailsUpdaterBatch
{
    public function __construct(
        private Server $server,
        private ?User $initiator = null,
        private string $queue = JobQueuesEnum::DEFAULT_QUEUE
    ) {
    }

    public function onQueue(string $queue): self
    {
        $this->queue = $queue;

        return $this;
    }

    /** @throws Throwable */
    public function dispatchSilently(): Batch
    {
        $this->server->setSilentUpdate();

        return $this->getPendingBatch()
            ->finally(fn () => $this->server->resetLoadingServerDetailsStates()->unsetSilentUpdate())
            ->onQueue($this->queue)
            ->dispatch();
    }

    /** @throws Throwable */
    public function dispatch(): Batch
    {
        return $this->getPendingBatch()
            ->finally(fn () => $this->server->resetLoadingServerDetailsStates())
            ->onQueue($this->queue)
            ->dispatch();
    }

    private function getPendingBatch(): PendingBatch
    {
        return Bus::batch([
            new UpdateServerHeight($this->server, $this->initiator),
        ])->then(function () {
            return Bus::batch([
                new CheckServerProcessStatus($this->server),
                new UpdateServerCoreVersion($this->server, $this->initiator),
                new UpdateServerPing($this->server, $this->initiator),
                new UpdateProcesses($this->server, $this->initiator),
                new UpdateServerResources($this->server, $this->initiator),
            ])
            ->onQueue($this->queue)
            ->dispatch();
        });
    }
}
