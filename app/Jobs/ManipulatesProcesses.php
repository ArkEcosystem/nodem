<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\AlertFailedTask;
use App\Jobs\Concerns\HandlesLogActivity;
use App\Models\Process;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Throwable;

abstract class ManipulatesProcesses implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use HandlesLogActivity;

    public User $initiator;

    public Process $process;

    public string $originalProcessStatus;

    public array $options;

    protected ?string $successStatus = null;

    public function __construct(User $initiator, Process $process, array $options = [])
    {
        $this->initiator = $initiator;

        $this->process = $process;

        $this->originalProcessStatus = $process->status;

        $this->options = $options;

        $this->setPendingStatus();
    }

    final public function handle(): void
    {
        $response = $this->execute();

        if (Arr::has($response, 'status')) {
            $this->process->markAs($response['status']);
        } elseif ($this->successStatus !== null) {
            $this->process->markAs($this->successStatus);
        }

        $this->logActivity($this->process->server, $this->initiator);
    }

    final public function failed(Throwable $e): void
    {
        AlertFailedTask::dispatch($this->process->server, $this->getAlertName(), $e, $this->initiator);

        // Meaning the manager could be offline
        if ($e instanceof ConnectionException) {
            $this->managerMightNotBeRunning();
            $this->process->markAs($this->originalProcessStatus);

            return;
        }

        $status = $this->process->fetchStatus() ?? $this->originalProcessStatus;

        $this->process->markAs($status);
    }

    abstract protected function execute(): array;

    abstract protected function activityDescription(): string;

    abstract protected function setPendingStatus(): void;

    abstract protected function getAlertName(): string;

    private function managerMightNotBeRunning(): void
    {
        $server = $this->process->server;

        // Dispatch the jobs related to checking the status of the manager
        UpdateServerPing::dispatch($server, $this->initiator);
        UpdateServerCoreVersion::dispatch($server, $this->initiator);
    }
}
