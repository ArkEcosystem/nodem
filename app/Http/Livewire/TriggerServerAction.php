<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Actions\AlertFailedTask;
use App\Enums\ServerTypeEnum;
use App\Enums\TeamMemberPermission;
use App\Jobs\DeleteProcess;
use App\Jobs\RestartProcess;
use App\Jobs\StartProcess;
use App\Jobs\StopProcess;
use App\Jobs\UpdateCore;
use App\Jobs\UpdateCoreManager;
use App\Jobs\UpdateProcesses;
use App\Services\Client\RPC;
use ARKEcosystem\Foundation\Fortify\Components\Concerns\InteractsWithUser;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Throwable;

/**
 * @property \App\Models\User $user
 */
final class TriggerServerAction extends Component
{
    use AuthorizesRequests;
    use InteractsWithUser;

    /** @var mixed */
    protected $listeners = ['triggerServerAction'];

    public function triggerServerAction(array $data): void
    {
        [$action, $processType, $serverId] = $data;

        $options = $data[3] ?? [];

        $server    = $this->user->servers()->findOrFail($serverId);

        // Since this task means a manual update we should remove the "silent
        // update" flag to ensure it always shows the loading indicator in case
        // the server is being updated in the background.
        $server->unsetSilentUpdate();

        $processes = $server->processes()->ofType($processType)->get();

        try {
            if ($action === 'start') {
                $this->authorize(TeamMemberPermission::SERVER_PROCESSES_START);

                if ($processes->isEmpty()) {
                    $processesTypes = $processType === 'all' ? ['forger', 'relay'] : $processType;

                    collect($processesTypes)->each(fn ($processType) => (bool) RPC::fromServer($server)->process()->start($processType, $options));

                    UpdateProcesses::dispatch($server, $this->user);
                } else {
                    $processes->each(fn ($process) => (bool) StartProcess::dispatch($this->user, $process, $options));
                }
            }

            if ($action === 'stop') {
                $this->authorize(TeamMemberPermission::SERVER_PROCESSES_STOP);

                $processes->each(fn ($process) => (bool) StopProcess::dispatch($this->user, $process, $options));
            }

            if ($action === 'restart') {
                $this->authorize(TeamMemberPermission::SERVER_PROCESSES_RESTART);

                $processes->each(fn ($process) => (bool) RestartProcess::dispatch($this->user, $process, $options));
            }

            if ($action === 'delete') {
                $this->authorize(TeamMemberPermission::SERVER_PROCESSES_DELETE);

                $processes->each(fn ($process) => (bool) DeleteProcess::dispatch($this->user, $process, $options));
            }

            if ($action === 'update' && $processType === ServerTypeEnum::CORE) {
                $this->authorize(TeamMemberPermission::CORE_UPDATE);

                UpdateCore::dispatch($server, $this->user);
            }

            if ($action === 'update' && $processType === ServerTypeEnum::CORE_MANAGER) {
                $this->authorize(TeamMemberPermission::CORE_MANAGER_UPDATE);

                UpdateCoreManager::dispatch($server, $this->user);
            }
        } catch (Throwable $e) {
            AlertFailedTask::dispatch($server, $e->getMessage(), $e, $this->user);
        }

        $this->emit('serverActionTriggered'.$server->id);
    }
}
