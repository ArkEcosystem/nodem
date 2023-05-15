<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Server;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Throwable;

abstract class HandlesLoadingState implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Batchable;

    public int $timeout = 10;

    public Server $server;

    public ?User $initiator;

    public function __construct(Server $server, ?User $initiator = null)
    {
        $this->initiator = $initiator;

        $this->server = $server->markTaskAsStarted($this->getTaskName());
    }

    final public function handle(): void
    {
        $this->execute();

        $this->server->markTaskAsSucceed($this->getTaskName());
    }

    final public function middleware(): array
    {
        return [
            (new WithoutOverlapping($this->server->getKey()))->expireAfter(10),
        ];
    }

    /**
     * In the case of the jobs that extend this class the alert name is the same
     * as the task name, but I am using a separate method to explicitly say that
     * the name represents an alert. This means that the alert name used here is
     * listed on the `AlertType` enum class and in the `ServerUpdatingTasksEnum`
     * enum class).
     */
    final protected function getAlertName(): string
    {
        return $this->getTaskName();
    }

    abstract protected function failed(Throwable $e): void;

    abstract protected function getTaskName(): string;

    abstract protected function execute(): void;
}
