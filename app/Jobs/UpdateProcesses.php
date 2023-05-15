<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Cache\ServerStore;
use App\Enums\ProcessStatusEnum;
use App\Enums\ServerProcessTypeEnum;
use App\Enums\ServerUpdatingTasksEnum;
use App\Jobs\Concerns\ManagesFailedTask;
use App\Models\Process;
use App\Services\Client\RPC;
use Illuminate\Support\Str;

final class UpdateProcesses extends HandlesLoadingState
{
    use ManagesFailedTask;

    protected function execute(): void
    {
        $response = RPC::fromServer($this->server)->process()->list();

        $this->server->processes()->delete();

        $processes = collect();
        foreach ($response as $process) {
            if (! Str::endsWith($process['name'], ['-core', '-forger', '-relay'])) {
                continue;
            }

            $processes->push([
                'server_id' => $this->server->id,
                'type'      => explode('-', $process['name'])[1],
                'name'      => $process['name'],
                'pid'       => $process['pm_id'],
                'cpu'       => $process['monit']['cpu'],
                'ram'       => $process['monit']['memory'] / 1024,
                'status'    => $process['status'],
            ]);
        }

        // Only update the online processes
        $coreProcess    = $processes->firstWhere('name', 'ark-core');
        $splitProcesses = $processes->whereIn('name', ['ark-relay', 'ark-forger']);
        if ($coreProcess !== null && $splitProcesses->count() > 0) {
            $hasSplitOnline = $splitProcesses->some('status', ProcessStatusEnum::ONLINE);
            if ($hasSplitOnline && $coreProcess['status'] !== ProcessStatusEnum::ONLINE) {
                $processes = $splitProcesses;
            } elseif (! $hasSplitOnline && $coreProcess['status'] === ProcessStatusEnum::ONLINE) {
                $processes = collect($coreProcess);
            } elseif ($this->server->process_type === ServerProcessTypeEnum::SEPARATE) {
                $processes = $splitProcesses;
            } else {
                $processes = collect($coreProcess);
            }
        }

        Process::upsert($processes->toArray(), ['server_id', 'type']);

        ServerStore::flush($this->server);
    }

    protected function getTaskName(): string
    {
        return ServerUpdatingTasksEnum::UPDATING_PROCESSES;
    }
}
